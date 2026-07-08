<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProvisioningService
{
    public function __construct(
        private readonly PterodactylService $pterodactyl,
    ) {}

    public function provision(Order $order): ?Server
    {
        $user = $order->user;
        $plan = $order->plan;

        $result = $this->pterodactyl->createServer($user, [
            'name' => ($plan->product->name ?? 'Game Server') . ' - ' . $user->name,
            'egg_id' => $plan->egg_id,
            'memory' => $plan->memory,
            'swap' => $plan->swap,
            'disk' => $plan->disk,
            'cpu' => $plan->cpu,
            'databases' => $plan->databases,
            'backups' => $plan->backups,
            'allocations' => $plan->allocations,
            'environment' => [],
        ]);

        if ($result) {
            $server = Server::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'pterodactyl_server_id' => $result['id'],
                'pterodactyl_server_identifier' => $result['identifier'],
                'name' => $result['name'] ?? ($plan->product->name . ' Server'),
                'status' => 'active',
                'cpu' => $plan->cpu,
                'memory' => $plan->memory,
                'disk' => $plan->disk,
                'node' => $result['node'],
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
