<?php

namespace App\Jobs;

use App\Models\Service;
use App\Services\DiscordService;
use App\Services\ProvisioningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProvisionServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public Service $service
    ) {}

    public function handle(ProvisioningService $provisioning, DiscordService $discord): void
    {
        $server = $provisioning->provision($this->service);

        if ($server) {
            $discord->sendNotification('new_order', [
                'user' => $this->service->user?->name ?? 'Unknown',
                'product' => $this->service->product?->name ?? 'Game Server',
                'amount' => $this->service->price ?? 0,
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to provision server', [
            'service_id' => $this->service->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
