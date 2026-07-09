<?php
namespace App\Jobs;

use App\Services\BillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 300;

    public function handle(BillingService $billing): void
    {
        $billing->generateRenewalInvoices();
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to generate invoices', [
            'error' => $exception->getMessage(),
        ]);
    }
}
