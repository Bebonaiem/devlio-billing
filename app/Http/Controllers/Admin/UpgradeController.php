<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceTransaction;
use App\Models\ServiceUpgrade;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\DB;

class UpgradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $upgrades = ServiceUpgrade::with(['service.product', 'service.user', 'plan', 'invoice'])
            ->latest()
            ->paginate(20);

        return view('admin.upgrades.index', compact('upgrades'));
    }

    public function show(ServiceUpgrade $upgrade)
    {
        $upgrade->load(['service.product', 'service.plan', 'service.user', 'plan', 'product', 'invoice']);

        return view('admin.upgrades.show', compact('upgrade'));
    }

    public function approve(ServiceUpgrade $upgrade)
    {
        return DB::transaction(function () use ($upgrade) {
            if ($upgrade->status !== 'pending') {
                return back()->with('error', 'Upgrade is not pending.');
            }

            $service = $upgrade->service;
            if (! $service) {
                return back()->with('error', 'Service not found.');
            }

            $newPlan = $upgrade->plan;
            $oldPlan = $service->plan;
            $priceDiff = 0;

            if ($newPlan && $oldPlan) {
                $currencyCode = $service->currency_code;
                $oldPrice = $oldPlan->prices()->where('currency_code', $currencyCode)->first();
                $newPrice = $newPlan->prices()->where('currency_code', $currencyCode)->first();
                if ($newPrice && $oldPrice) {
                    $priceDiff = round(max(0, (float) $newPrice->price - (float) $oldPrice->price), 2);
                }
            }

            if ($priceDiff > 0) {
                $invoice = app(InvoiceService::class)->createInvoice($service->user, [
                    [
                        'quantity' => 1,
                        'price' => $priceDiff,
                        'description' => 'Upgrade: '.($service->product->name ?? '').' → '.($newPlan->name ?? ''),
                        'reference_id' => $service->id,
                        'reference_type' => $service::class,
                    ],
                ], $service->currency_code);

                $transaction = InvoiceTransaction::create([
                    'invoice_id' => $invoice->id,
                    'gateway_id' => null,
                    'amount' => $priceDiff,
                    'fee' => 0,
                    'transaction_id' => 'UPGRADE-'.strtoupper(uniqid()),
                    'status' => 'succeeded',
                    'is_credit_transaction' => false,
                ]);

                app(InvoiceService::class)->markPaid($invoice, $transaction);
            }

            $service->update([
                'plan_id' => $newPlan->id,
                'price' => $newPlan->prices()
                    ->where('currency_code', $service->currency_code)
                    ->first()?->price ?? $service->price,
            ]);

            $upgrade->update(['status' => 'approved']);

            return back()->with('success', 'Upgrade approved and applied.');
        });
    }

    public function deny(ServiceUpgrade $upgrade)
    {
        $upgrade->update(['status' => 'denied']);

        return back()->with('success', 'Upgrade denied.');
    }

    public function destroy(ServiceUpgrade $upgrade)
    {
        $upgrade->delete();

        return redirect()->route('admin.upgrades.index')
            ->with('success', 'Upgrade deleted.');
    }
}
