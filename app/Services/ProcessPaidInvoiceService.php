¿<?php
namespace App\Services;

use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\ServiceUpgrade;

class ProcessPaidInvoiceService
{
    public function handle(Invoice $invoice): void
    {
        $invoice->items->each(function ($item) use ($invoice) {
            if ($item->reference_type === Service::class) {
                $service = Service::find($item->reference_id);

                if ($service) {
                    app(RenewServiceService::class)->handle($service);
                }
            } elseif ($item->reference_type === ServiceUpgrade::class) {
                $upgrade = ServiceUpgrade::find($item->reference_id);

                if ($upgrade && $upgrade->status === ServiceUpgrade::STATUS_PENDING) {
                    app(ServiceUpgradeService::class)->handle($upgrade);
                }
            } elseif ($item->reference_type === Credit::class) {
                $credit = $invoice->user->credits()
                    ->where('currency_code', $invoice->currency_code)
                    ->first();

                if ($credit) {
                    $credit->increment('amount', $item->price);
                } else {
                    $invoice->user->credits()->create([
                        'currency_code' => $invoice->currency_code,
                        'amount' => $item->price,
                    ]);
                }
            }
        });
    }
}
