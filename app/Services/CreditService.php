<?php
namespace App\Services;

use App\Models\Credit;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreditService
{
    public function getBalance(User $user, string $currencyCode = 'USD'): float
    {
        $credit = Credit::where('user_id', $user->id)
            ->where('currency_code', $currencyCode)
            ->first();

        return $credit ? (float) $credit->amount : 0.0;
    }

    public function add(User $user, float $amount, string $currencyCode = 'USD'): void
    {
        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($user, $amount, $currencyCode) {
            Credit::updateOrCreate(
                ['user_id' => $user->id, 'currency_code' => $currencyCode],
                ['amount' => DB::raw('amount + '.(float) $amount)]
            );
        });
    }

    public function deduct(User $user, float $amount, string $currencyCode = 'USD'): bool
    {
        if ($amount <= 0) {
            return false;
        }

        return DB::transaction(function () use ($user, $amount, $currencyCode) {
            $credit = Credit::where('user_id', $user->id)
                ->where('currency_code', $currencyCode)
                ->lockForUpdate()
                ->first();

            if (! $credit || $credit->amount < $amount) {
                return false;
            }

            $credit->decrement('amount', $amount);

            return true;
        });
    }

    public function applyToInvoice(User $user, Invoice $invoice): float
    {
        $currencyCode = $invoice->currency_code;
        $balance = $this->getBalance($user, $currencyCode);

        if ($balance <= 0) {
            return 0.0;
        }

        $totals = app(InvoiceService::class)->calculateTotal($invoice);
        $amountToApply = min($balance, $totals['total']);

        if ($amountToApply <= 0) {
            return 0.0;
        }

        DB::transaction(function () use ($user, $invoice, $amountToApply, $currencyCode) {
            $credit = Credit::where('user_id', $user->id)
                ->where('currency_code', $currencyCode)
                ->lockForUpdate()
                ->first();

            if (! $credit || $credit->amount < $amountToApply) {
                return;
            }

            $credit->decrement('amount', $amountToApply);

            InvoiceTransaction::create([
                'invoice_id' => $invoice->id,
                'amount' => $amountToApply,
                'fee' => 0,
                'transaction_id' => 'CREDIT-'.strtoupper(uniqid()),
                'status' => 'succeeded',
                'is_credit_transaction' => true,
            ]);
        });

        return $amountToApply;
    }

    public function setBalance(User $user, float $amount, string $currencyCode = 'USD'): void
    {
        DB::transaction(function () use ($user, $amount, $currencyCode) {
            Credit::updateOrCreate(
                ['user_id' => $user->id, 'currency_code' => $currencyCode],
                ['amount' => max(0, $amount)]
            );
        });
    }
}
