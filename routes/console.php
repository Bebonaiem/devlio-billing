<?php

use App\Jobs\GenerateInvoices;
use App\Jobs\ProcessBillingCycle;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new GenerateInvoices)->dailyAt('00:01');
Schedule::job(new ProcessBillingCycle)->dailyAt('00:15');

Artisan::command('billing:run', function () {
    dispatch_sync(new GenerateInvoices);
    dispatch_sync(new ProcessBillingCycle);
    $this->info('Billing cycle processed successfully.');
})->purpose('Run billing cycle manually');
