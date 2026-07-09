¿<?php
namespace App\Services;

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
        $this->baseUrl = rtrim(config('services.pterodactyl.panel_url') ?? '', '/');
        $this->apiKey = config('services.pterodactyl.api_key') ?? '';
    }

    private function applicationRequest(string $method, string $endpoint, array $data = []): Response
    {
        $url = $this->baseUrl.'/api/application/'.ltrim($endpoint, '/');

        return Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->$method($url, $data);
    }

    private function safeRequest(string $method, string $endpoint, array $data = []): ?Response
    {
        try {
            return $this->applicationRequest($method, $endpoint, $data);
        } catch (\Exception $e) {
            report($e);

            return null;
        }
    }

    public function testConnection(): bool
    {
        $response = $this->safeRequest('get', '/users?per_page=1');

        return $response?->successful() ?? false;
    }

    public function createUser(User $user): ?string
    {
        $response = $this->safeRequest('post', '/users', [
            'username' => strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $user->name)).'_'.$user->id,
            'email' => $user->email,
            'first_name' => explode(' ', $user->name, 2)[0] ?? $user->name,
            'last_name' => explode(' ', $user->name, 2)[1] ?? '',
            'password' => bin2hex(random_bytes(16)),
        ]);

        if ($response && $response->successful()) {
            $attributes = $response->json('attributes') ?? $response->json('data.attributes');
            $pterodactylId = $attributes['id'] ?? null;

            if ($pterodactylId) {
                $apiKey = $this->createClientApiKey($pterodactylId, $user);
                $user->update([
                    'pterodactyl_user_id' => $pterodactylId,
                    'pterodactyl_api_key' => $apiKey,
                ]);

                return $pterodactylId;
            }
        }

        Log::error('Failed to create Pterodactyl user', [
            'user_id' => $user->id,
            'response' => $response?->body() ?? 'No response',
        ]);

        return null;
    }

    public function createClientApiKey(int $pterodactylUserId, User $user): ?string
    {
        $response = $this->safeRequest('post', "/users/{$pterodactylUserId}/api-keys", [
            'description' => 'Client API key for '.($user->email ?? $user->id),
            'allowed_ips' => ['*'],
        ]);

        if ($response && $response->successful()) {
            $attributes = $response->json('attributes') ?? [];

            return $attributes['token'] ?? null;
        }

        return null;
    }

    public function createServer(User $user, array $data): ?array
    {
        $pterodactylUserId = $user->pterodactyl_user_id;

        if (! $pterodactylUserId) {
            $pterodactylUserId = $this->createUser($user);
            if (! $pterodactylUserId) {
                return null;
            }
        }

        if (empty($data['docker_image']) || empty($data['startup'])) {
            $nestId = (int) ($data['nest_id'] ?? 1);
            $eggId = (int) ($data['egg_id'] ?? 1);
            $egg = $this->getEggDetails($nestId, $eggId);

            if ($egg) {
                $data['docker_image'] = $data['docker_image'] ?? ($egg['docker_image'] ?? null);
                $data['startup'] = $data['startup'] ?? ($egg['startup'] ?? null);
            }
        }

        $allocationId = (int) ($data['allocation_id'] ?? 0);
        if ($allocationId <= 0) {
            $allocationId = $this->getFreeAllocationId() ?? 0;
        }

        $response = $this->safeRequest('post', '/servers', [
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
                'default' => $allocationId,
            ],
        ]);

        if ($response && $response->successful()) {
            $attributes = $response->json('attributes') ?? $response->json('data.attributes');

            return [
                'id' => $attributes['id'] ?? null,
                'identifier' => $attributes['identifier'] ?? null,
                'node' => $attributes['node'] ?? null,
            ];
        }

        Log::error('Failed to create Pterodactyl server', [
            'user_id' => $user->id,
            'response' => $response?->body() ?? 'No response',
        ]);

        return null;
    }

    public function suspendServer(string $serverId): bool
    {
        $response = $this->safeRequest('post', "/servers/{$serverId}/suspend");

        return $response?->successful() ?? false;
    }

    public function unsuspendServer(string $serverId): bool
    {
        $response = $this->safeRequest('post', "/servers/{$serverId}/unsuspend");

        return $response?->successful() ?? false;
    }

    public function terminateServer(string $serverId): bool
    {
        $response = $this->safeRequest('delete', "/servers/{$serverId}");

        return $response?->successful() ?? false;
    }

    public function getServerDetails(string $serverId): ?array
    {
        $response = $this->safeRequest('get', "/servers/{$serverId}");

        if ($response && $response->successful()) {
            return $response->json('attributes') ?? $response->json('data.attributes');
        }

        return null;
    }

    public function getNests(): array
    {
        $response = $this->safeRequest('get', '/nests');

        return $response?->successful() ? ($response->json('data') ?? []) : [];
    }

    public function getEggs(int $nestId): array
    {
        $response = $this->safeRequest('get', "/nests/{$nestId}/eggs");

        return $response?->successful() ? ($response->json('data') ?? []) : [];
    }

    public function getEggDetails(int $nestId, int $eggId): ?array
    {
        $response = $this->safeRequest('get', "/nests/{$nestId}/eggs/{$eggId}");

        return $response?->successful() ? ($response->json('attributes') ?? $response->json('data.attributes')) : null;
    }

    public function getLocations(): array
    {
        $response = $this->safeRequest('get', '/locations');

        return $response?->successful() ? ($response->json('data') ?? []) : [];
    }

    public function getNodes(): array
    {
        $response = $this->safeRequest('get', '/nodes');

        return $response?->successful() ? ($response->json('data') ?? []) : [];
    }

    public function getNodeAllocations(int $nodeId): array
    {
        $response = $this->safeRequest('get', "/nodes/{$nodeId}/allocations");

        return $response?->successful() ? ($response->json('data') ?? []) : [];
    }

    private function getFreeAllocationId(): ?int
    {
        $nodes = $this->getNodes();

        foreach ($nodes as $node) {
            $nodeId = $node['attributes']['id'] ?? ($node['id'] ?? null);

            if (! $nodeId) {
                continue;
            }

            $allocations = $this->getNodeAllocations($nodeId);

            foreach ($allocations as $allocation) {
                $attrs = $allocation['attributes'] ?? $allocation;

                if (empty($attrs['server_id'])) {
                    return (int) ($attrs['id'] ?? null);
                }
            }
        }

        return null;
    }

    public function getServerResources(string $serverIdentifier, User $user): ?array
    {
        $apiKey = $user->pterodactyl_api_key;

        if (! $apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl."/api/client/servers/{$serverIdentifier}/resources");

            if ($response->successful()) {
                return $response->json('attributes');
            }
        } catch (\Exception $e) {
            report($e);
        }

        return null;
    }
}
