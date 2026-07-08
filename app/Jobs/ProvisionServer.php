<?php

namespace App\Jobs;

use App\Models\Service;
use App\Services\DiscordService;
use App\Services\ProvisioningService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProvisionServer implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Service $service
    ) {}

    public function handle(ProvisioningService $provisioning, DiscordService $discord): void
    {
        $server = $provisioning->provision($this->service);

        if ($server) {
            $discord->sendNotification('new_order', [
                'user' => $this->service->user->name,
                'product' => $this->service->product?->name ?? 'Game Server',
                'amount' => $this->service->price ?? 0,
            ]);
        }
    }
}
