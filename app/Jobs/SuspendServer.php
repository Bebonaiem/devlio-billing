<?php

namespace App\Jobs;

use App\Models\Server;
use App\Services\ProvisioningService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SuspendServer implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Server $server
    ) {}

    public function handle(ProvisioningService $provisioning): void
    {
        $provisioning->suspend($this->server);
    }
}
