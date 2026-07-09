¿<?php
namespace App\Services;

use App\Helpers\ExtensionHelper;
use App\Models\ServiceUpgrade;
use Illuminate\Support\Facades\DB;

class ServiceUpgradeService
{
    public function handle(ServiceUpgrade $serviceUpgrade): void
    {
        DB::transaction(function () use ($serviceUpgrade) {
            $serviceUpgrade->update(['status' => ServiceUpgrade::STATUS_COMPLETED]);

            $service = $serviceUpgrade->service;

            if ($service->product->stock !== null) {
                $service->product->increment('stock', $service->quantity);
            }

            $service->update([
                'plan_id' => $serviceUpgrade->plan_id,
                'product_id' => $serviceUpgrade->product_id,
            ]);

            $service->refresh();

            if ($service->product->stock !== null) {
                $service->product->decrement('stock', $service->quantity);
            }

            $newIds = $serviceUpgrade->configs->pluck('config_option_id');
            $service->configs()->whereNotIn('config_option_id', $newIds)->delete();

            foreach ($serviceUpgrade->configs as $config) {
                $service->configs()->updateOrCreate(
                    ['config_option_id' => $config->config_option_id],
                    ['config_value_id' => $config->config_value_id]
                );
            }

            $service->refresh();
            $service->price = $service->calculatePrice();
            $service->save();

            $pendingInvoice = $service->invoices()->where('status', 'pending')->first();
            if ($pendingInvoice) {
                $item = $pendingInvoice->items()
                    ->where('reference_type', Service::class)
                    ->where('reference_id', $service->id)
                    ->first();

                if ($item) {
                    $item->update(['price' => $service->price]);
                }
            }

            if ($service->product && $service->product->server) {
                ExtensionHelper::upgradeServer($service);
            }
        });
    }
}
