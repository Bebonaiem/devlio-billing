¿<?php
namespace App\Jobs;

use App\Models\Server;
use App\Services\ProvisioningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UnsuspendServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public Server $server
    ) {}

    public function handle(ProvisioningService $provisioning): void
    {
        $provisioning->unsuspend($this->server);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to unsuspend server', [
            'server_id' => $this->server->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
