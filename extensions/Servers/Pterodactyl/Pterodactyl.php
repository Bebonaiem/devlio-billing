<?php

namespace App\Extensions\Servers\Pterodactyl;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Server;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Http;

#[ExtensionMeta(
    name: 'Pterodactyl',
    description: 'Game server provisioning via Pterodactyl Panel',
    version: '1.0.0',
    author: 'GameBilling',
    url: 'https://pterodactyl.io'
)]
class Pterodactyl extends Server
{
    public function getConfig(array $values = []): array
    {
        return [
            [
                'name' => 'panel_url',
                'label' => 'Panel URL',
                'type' => 'url',
                'description' => 'The URL of your Pterodactyl panel',
                'required' => true,
                'placeholder' => 'https://panel.example.com',
            ],
            [
                'name' => 'api_key',
                'label' => 'API Key',
                'type' => 'password',
                'description' => 'Application API key with admin permissions',
                'required' => true,
                'encrypted' => true,
            ],
        ];
    }

    public function getProductConfig(array $values = []): array
    {
        $nodes = $this->apiRequest('/api/application/nests')['data'] ?? [];
        $locations = $this->apiRequest('/api/application/locations')['data'] ?? [];

        $nestOptions = [];
        foreach ($nodes as $nest) {
            $nestOptions[$nest['attributes']['id']] = $nest['attributes']['name'];
        }

        $locationOptions = [];
        foreach ($locations as $location) {
            $locationOptions[$location['attributes']['id']] = $location['attributes']['name'];
        }

        return [
            [
                'name' => 'nest_id',
                'label' => 'Nest',
                'type' => 'select',
                'options' => $nestOptions,
                'required' => true,
                'description' => 'Select the nest for this product',
            ],
            [
                'name' => 'egg_id',
                'label' => 'Egg',
                'type' => 'select',
                'options' => $this->getEggs($values['nest_id'] ?? null),
                'required' => true,
                'description' => 'Select the egg for this product',
                'live' => true,
            ],
            [
                'name' => 'location_id',
                'label' => 'Location',
                'type' => 'select',
                'options' => $locationOptions,
                'required' => true,
                'description' => 'Select the location for this product',
            ],
            [
                'name' => 'auto_deploy',
                'label' => 'Auto Deploy',
                'type' => 'checkbox',
                'description' => 'Automatically assign node and allocation',
                'default' => true,
            ],
            [
                'name' => 'startup_command',
                'label' => 'Startup Command',
                'type' => 'textarea',
                'description' => 'Override the startup command (leave empty for egg default)',
            ],
        ];
    }

    public function getCheckoutConfig(object $product, array $values = []): array
    {
        $eggDetails = $this->getEggDetails($values['egg_id'] ?? null);

        if (empty($eggDetails)) {
            return [];
        }

        $config = [];
        foreach ($eggDetails['attributes']['config'] ?? [] as $field) {
            if ($field['env_variable'] === 'SERVER_JARFILE' || $field['type'] === 'text') {
                $config[] = [
                    'name' => $field['env_variable'],
                    'label' => $field['name'],
                    'type' => 'text',
                    'description' => $field['description'] ?? '',
                    'required' => $field['required'] ?? false,
                    'default' => $field['default_value'] ?? '',
                ];
            }
        }

        return $config;
    }

    public function createServer(Service $service, array $settings, array $properties): array
    {
        $user = $service->user;
        $plan = $service->plan;
        $product = $service->product;

        $panelUser = $this->ensureUser($user, $settings);

        $nodeId = $product->getSetting('node_id');
        $allocation = $this->getAllocation($nodeId, $settings);

        $eggId = $plan->getSetting('egg_id');
        $nestId = $plan->getSetting('nest_id');

        $serverData = [
            'name' => $service->label ?? "Service #{$service->id}",
            'description' => "Game server for {$user->name}",
            'user' => $panelUser['id'],
            'egg' => $eggId,
            'docker_image' => $this->getEggDockerImage($eggId, $settings),
            'startup' => $plan->getSetting('startup_command') ?? $this->getEggStartup($eggId, $settings),
            'environment' => $this->buildEnvironment($properties),
            'limits' => [
                'memory' => $plan->memory * 1024,
                'swap' => $plan->swap * 1024,
                'disk' => $plan->disk * 1024,
                'io' => 500,
                'cpu' => $plan->cpu,
            ],
            'feature_limits' => [
                'databases' => $plan->databases,
                'allocations' => $plan->allocations,
                'backups' => $plan->backups,
            ],
            'deploy' => [
                'locations' => [$plan->getSetting('location_id') ?? $allocation['attributes']['location_id']],
                'dedicated_ip' => false,
                'port_range' => [],
            ],
            'start_on_completion' => false,
            'skip_scripts' => false,
            'oom_disabled' => false,
        ];

        $response = $this->apiRequest('/api/application/servers', 'POST', $serverData);

        return [
            'pterodactyl_server_id' => $response['data']['attributes']['id'],
            'pterodactyl_server_identifier' => $response['data']['attributes']['uuid'],
            'ip' => $allocation['attributes']['ip'],
        ];
    }

    public function suspendServer(Service $service, array $settings, array $properties): void
    {
        $this->apiRequest(
            "/api/application/servers/{$service->server->pterodactyl_server_id}/suspend",
            'POST'
        );
    }

    public function unsuspendServer(Service $service, array $settings, array $properties): void
    {
        $this->apiRequest(
            "/api/application/servers/{$service->server->pterodactyl_server_id}/unsuspend",
            'POST'
        );
    }

    public function terminateServer(Service $service, array $settings, array $properties): void
    {
        $this->apiRequest(
            "/api/application/servers/{$service->server->pterodactyl_server_id}",
            'DELETE'
        );
    }

    public function upgradeServer(Service $service, array $settings, array $properties): void
    {
        $plan = $service->plan;

        $this->apiRequest(
            "/api/application/servers/{$service->server->pterodactyl_server_id}/build",
            'PATCH',
            [
                'limits' => [
                    'memory' => $plan->memory * 1024,
                    'swap' => $plan->swap * 1024,
                    'disk' => $plan->disk * 1024,
                    'io' => 500,
                    'cpu' => $plan->cpu,
                ],
                'feature_limits' => [
                    'databases' => $plan->databases,
                    'allocations' => $plan->allocations,
                    'backups' => $plan->backups,
                ],
            ]
        );
    }

    public function getActions(Service $service): array
    {
        $server = $service->server;

        if (! $server) {
            return [];
        }

        $panelUrl = $this->config('panel_url');

        return [
            [
                'label' => 'Go to Server',
                'url' => $panelUrl.'/server/'.$server->pterodactyl_server_identifier,
                'icon' => 'external-link',
            ],
        ];
    }

    public function testConfig(): bool
    {
        try {
            $response = $this->apiRequest('/api/application/nests');

            return isset($response['data']);
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function apiRequest(string $endpoint, string $method = 'GET', array $data = []): array
    {
        $panelUrl = $this->config('panel_url');
        $apiKey = $this->config('api_key');

        $url = rtrim($panelUrl, '/').$endpoint;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Accept' => 'Application/Json',
        ]);

        $response = match ($method) {
            'GET' => $response->get($url),
            'POST' => $response->post($url, $data),
            'PATCH' => $response->patch($url, $data),
            'DELETE' => $response->delete($url),
            default => $response->get($url),
        };

        if ($response->failed()) {
            throw new \RuntimeException('Pterodactyl API request failed: '.$response->body());
        }

        return $response->json();
    }

    protected function ensureUser(User $user, array $settings): array
    {
        $existing = $this->apiRequest("/api/application/users?filter[email={$user->email}]");

        if (! empty($existing['data'])) {
            return $existing['data'][0]['attributes'];
        }

        $response = $this->apiRequest('/api/application/users', 'POST', [
            'external_id' => (string) $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->email,
            'password' => bcrypt(Str::random(32)),
        ]);

        return $response['data']['attributes'];
    }

    protected function getAllocation(int $nodeId, array $settings): array
    {
        $response = $this->apiRequest("/api/application/nodes/{$nodeId}/allocations");

        $allocations = collect($response['data'] ?? []);
        $available = $allocations->first(fn ($a) => ! $a['attributes']['assigned']);

        if (! $available) {
            throw new \RuntimeException('No available allocations on node {$nodeId}');
        }

        return $available;
    }

    protected function getEggs(?int $nestId): array
    {
        if (! $nestId) {
            return [];
        }

        $response = $this->apiRequest("/api/application/nests/{$nestId}/eggs");

        $eggs = [];
        foreach ($response['data'] ?? [] as $egg) {
            $eggs[$egg['attributes']['id']] = $egg['attributes']['name'];
        }

        return $eggs;
    }

    protected function getEggDetails(?int $eggId): ?array
    {
        if (! $eggId) {
            return null;
        }

        $response = $this->apiRequest("/api/application/eggs/{$eggId}");

        return $response['data'] ?? null;
    }

    protected function getEggDockerImage(int $eggId, array $settings): string
    {
        $egg = $this->getEggDetails($eggId);

        return $egg['attributes']['docker_image'] ?? 'ghcr.io/pterodactyl/yolks:java_17';
    }

    protected function getEggStartup(int $eggId, array $settings): string
    {
        $egg = $this->getEggDetails($eggId);

        return $egg['attributes']['startup'] ?? '';
    }

    protected function buildEnvironment(array $properties): array
    {
        return array_filter($properties, fn ($v, $k) => ! in_array($k, ['node_id', 'location_id', 'nest_id', 'egg_id']), ARRAY_FILTER_USE_BOTH);
    }
}
