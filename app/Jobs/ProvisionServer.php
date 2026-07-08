<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\DiscordService;
use App\Services\ProvisioningService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProvisionServer implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function handle(ProvisioningService $provisioning, DiscordService $discord): void
    {
        $server = $provisioning->provision($this->order);

        if ($server) {
            $discord->sendNotification('new_order', [
                'user' => $this->order->user->name,
                'product' => $this->order->plan?->product?->name ?? 'Game Server',
                'amount' => $this->order->plan?->price ?? 0,
            ]);
        }
    }
}
