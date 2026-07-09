<?php
namespace App\Services;

use App\Jobs\ProvisionServer;
use App\Jobs\UnsuspendServer;
use App\Models\Service;

class RenewServiceService
{
    public function handle(Service $service): void
    {
        if ($service->product && $service->product->server) {
            if ($service->status === Service::STATUS_SUSPENDED) {
                UnsuspendServer::dispatch($service);
            } elseif ($service->status === Service::STATUS_PENDING) {
                ProvisionServer::dispatch($service);
            }
        }

        $service->expires_at = $service->calculateNextDueDate();
        $service->status = Service::STATUS_ACTIVE;
        $service->save();
    }
}
