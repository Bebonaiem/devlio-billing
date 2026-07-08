<?php

namespace App\Jobs;

use App\Services\BillingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessBillingCycle implements ShouldQueue
{
    use Queueable;

    public function handle(BillingService $billing): void
    {
        $billing->markOverdueInvoices();
        $billing->processOverdueSuspensions();
        $billing->processTerminations();
    }
}
