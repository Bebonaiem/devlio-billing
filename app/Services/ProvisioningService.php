<?php

namespace App\Services;

use App\Models\Server;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProvisioningService
{
    public function __construct(
        private readonly PterodactylService $pterodactyl,
    ) {}

    public function provision(Service $service): ?Server
    {
        $user = $service->user;
        $plan = $service->plan;
        $product = $service->product;

        $result = $this->pterodactyl->createServer($user, [
            'name' => ($product->name ?? 'Server') . ' - ' . $user->name,
            'egg_id' => $plan?->egg_id ?? 1,
            'memory' => $plan?->memory ?? 1024,
            'swap' => $plan?->swap ?? 0,
            'disk' => $plan?->disk ?? 1024,
            'cpu' => $plan?->cpu ?? 100,
            'databases' => $plan?->databases ?? 0,
            'backups' => $plan?->backups ?? 0,
            'allocations' => $plan?->allocations ?? 1,
            'environment' => [],
        ]);

        if ($result) {
            $server = Server::create([
                'service_id' => $service->id,
                'order_id' => $service->order_id,
                'user_id' => $user->id,
                'pterodactyl_server_id' => $result['id'],
                'pterodactyl_server_identifier' => $result['identifier'],
                'name' => $result['name'] ?? ($product->name . ' Server'),
                'status' => 'active',
                'cpu' => $plan?->cpu ?? null,
                'memory' => $plan?->memory ?? null,
                'disk' => $plan?->disk ?? null,
                'node' => $result['node'] ?? null,
            ]);

            return $server;
        }

        return null;
    }

    public function suspend(Server $server): bool
    {
        if (!$server->pterodactyl_server_id) {
            return false;
        }

        $success = $this->pterodactyl->suspendServer($server->pterodactyl_server_id);

        if ($success) {
            $server->update(['status' => 'suspended']);
        }

        return $success;
    }

    public function unsuspend(Server $server): bool
    {
        if (!$server->pterodactyl_server_id) {
            return false;
        }

        $success = $this->pterodactyl->unsuspendServer($server->pterodactyl_server_id);

        if ($success) {
            $server->update(['status' => 'active']);
        }

        return $success;
    }

    public function terminate(Server $server): bool
    {
        if (!$server->pterodactyl_server_id) {
            return false;
        }

        $success = $this->pterodactyl->terminateServer($server->pterodactyl_server_id);

        if ($success) {
            $server->update(['status' => 'terminated']);
        }

        return $success;
    }
}