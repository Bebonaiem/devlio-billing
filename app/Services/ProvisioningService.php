<?php
namespace App\Services;

use App\Helpers\ExtensionHelper;
use App\Models\Server;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class ProvisioningService
{
    public function provision(Service $service): ?Server
    {
        $product = $service->product;

        if (! $product || ! $product->server) {
            Log::error('Cannot provision service: no server extension configured', [
                'service_id' => $service->id,
            ]);

            return null;
        }

        try {
            $result = ExtensionHelper::createServer($service);

            if ($result) {
                return Server::create([
                    'service_id' => $service->id,
                    'order_id' => $service->order_id,
                    'user_id' => $service->user_id,
                    'pterodactyl_server_id' => $result['pterodactyl_server_id'] ?? null,
                    'pterodactyl_server_identifier' => $result['pterodactyl_server_identifier'] ?? null,
                    'name' => $result['name'] ?? $product->name.' Server',
                    'status' => 'active',
                    'ip' => $result['ip'] ?? null,
                    'cpu' => $service->plan?->cpu,
                    'memory' => $service->plan?->memory,
                    'disk' => $service->plan?->disk,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Server provisioning failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    public function suspend(Service $service): bool
    {
        $server = $service->server;

        if (! $server) {
            return false;
        }

        try {
            ExtensionHelper::suspendServer($service);
            $server->update(['status' => 'suspended']);

            return true;
        } catch (\Throwable $e) {
            Log::error('Server suspension failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function unsuspend(Service $service): bool
    {
        $server = $service->server;

        if (! $server) {
            return false;
        }

        try {
            ExtensionHelper::unsuspendServer($service);
            $server->update(['status' => 'active']);

            return true;
        } catch (\Throwable $e) {
            Log::error('Server unsuspension failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function terminate(Service $service): bool
    {
        $server = $service->server;

        if (! $server) {
            return false;
        }

        try {
            ExtensionHelper::terminateServer($service);
            $server->update(['status' => 'terminated']);

            return true;
        } catch (\Throwable $e) {
            Log::error('Server termination failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function upgrade(Service $service): bool
    {
        $server = $service->server;

        if (! $server) {
            return false;
        }

        try {
            ExtensionHelper::upgradeServer($service);

            return true;
        } catch (\Throwable $e) {
            Log::error('Server upgrade failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
