<?php

namespace App\Services;

use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PterodactylService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.pterodactyl.panel_url'), '/');
        $this->apiKey = config('services.pterodactyl.api_key');
    }

    private function applicationRequest(string $method, string $endpoint, array $data = []): Response
    {
        $url = $this->baseUrl . '/api/application/' . ltrim($endpoint, '/');

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->$method($url, $data);
    }

    private function clientRequest(string $method, string $endpoint, array $data = []): Response
    {
        $url = $this->baseUrl . '/api/client/' . ltrim($endpoint, '/');

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->$method($url, $data);
    }

    public function testConnection(): bool
    {
        $response = $this->applicationRequest('get', '/users?per_page=1');
        return $response->successful();
    }

    public function createUser(User $user): ?string
    {
        $response = $this->applicationRequest('post', '/users', [
            'username' => strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $user->name)) . '_' . $user->id,
            'email' => $user->email,
            'first_name' => explode(' ', $user->name, 2)[0] ?? $user->name,
            'last_name' => explode(' ', $user->name, 2)[1] ?? '',
            'password' => bin2hex(random_bytes(16)),
        ]);

        if ($response->successful()) {
            $pterodactylId = $response->json('attributes.id');
            // Generate a client API key for this user
            $apiKey = $this->createClientApiKey($pterodactylId, $user);
            $user->update([
                'pterodactyl_user_id' => $pterodactylId,
                'pterodactyl_api_key' => $apiKey,
            ]);
            return $pterodactylId;
        }

        Log::error('Failed to create Pterodactyl user', [
            'user_id' => $user->id,
            'response' => $response->body(),
        ]);

        return null;
    }

    public function createClientApiKey(int $pterodactylUserId, User $user): ?string
    {
        // Note: Creating client API keys requires the Pterodactyl addon or direct DB access.
        // For now, we use the application API key for admin operations.
        // This is a simplified approach - in production, use the addon endpoint.
        return null;
    }

    public function createServer(User $user, array $data): ?array
    {
        $pterodactylUserId = $user->pterodactyl_user_id;

        if (!$pterodactylUserId) {
            $pterodactylUserId = $this->createUser($user);
            if (!$pterodactylUserId) {
                return null;
            }
        }

        $response = $this->applicationRequest('post', '/servers', [
            'name' => $data['name'],
            'user' => (int) $pterodactylUserId,
            'egg' => (int) $data['egg_id'],
            'docker_image' => $data['docker_image'] ?? null,
            'startup' => $data['startup'] ?? null,
            'environment' => $data['environment'] ?? [],
            'limits' => [
                'memory' => (int) $data['memory'],
                'swap' => (int) ($data['swap'] ?? 0),
                'disk' => (int) $data['disk'],
                'io' => 500,
                'cpu' => (int) ($data['cpu'] ?? 100),
            ],
            'feature_limits' => [
                'databases' => (int) ($data['databases'] ?? 0),
                'backups' => (int) ($data['backups'] ?? 0),
                'allocations' => (int) ($data['allocations'] ?? 1),
            ],
            'allocation' => [
                'default' => (int) $data['allocation_id'],
            ],
        ]);

        if ($response->successful()) {
            $attributes = $response->json('attributes');
            return [
                'id' => $attributes['id'],
                'identifier' => $attributes['identifier'],
                'node' => $attributes['node'] ?? null,
            ];
        }

        Log::error('Failed to create Pterodactyl server', [
            'user_id' => $user->id,
            'response' => $response->body(),
        ]);

        return null;
    }

    public function suspendServer(string $serverId): bool
    {
        $response = $this->applicationRequest('post', "/servers/{$serverId}/suspend");
        return $response->successful();
    }

    public function unsuspendServer(string $serverId): bool
    {
        $response = $this->applicationRequest('post', "/servers/{$serverId}/unsuspend");
        return $response->successful();
    }

    public function terminateServer(string $serverId): bool
    {
        $response = $this->applicationRequest('delete', "/servers/{$serverId}");
        return $response->successful();
    }

    public function getServerDetails(string $serverId): ?array
    {
        $response = $this->applicationRequest('get', "/servers/{$serverId}");

        if ($response->successful()) {
            return $response->json('attributes');
        }

        return null;
    }

    public function getNests(): array
    {
        $response = $this->applicationRequest('get', '/nests');
        return $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function getEggs(int $nestId): array
    {
        $response = $this->applicationRequest('get', "/nests/{$nestId}/eggs");
        return $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function getEggDetails(int $nestId, int $eggId): ?array
    {
        $response = $this->applicationRequest('get', "/nests/{$nestId}/eggs/{$eggId}");
        return $response->successful() ? $response->json('attributes') : null;
    }

    public function getLocations(): array
    {
        $response = $this->applicationRequest('get', '/locations');
        return $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function getNodes(): array
    {
        $response = $this->applicationRequest('get', '/nodes');
        return $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function getNodeAllocations(int $nodeId): array
    {
        $response = $this->applicationRequest('get', "/nodes/{$nodeId}/allocations");
        return $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function getServerResources(string $serverIdentifier, User $user): ?array
    {
        $apiKey = $user->pterodactyl_api_key ?? $this->apiKey;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ])->get($this->baseUrl . "/api/client/servers/{$serverIdentifier}/resources");

        if ($response->successful()) {
            return $response->json('attributes');
        }

        return null;
    }
}
