<?php
namespace App\Classes\Extension;

use App\Models\BillingAgreement;
use App\Models\Invoice;
use App\Models\User;

abstract class Gateway extends Extension
{
    abstract public function pay(Invoice $invoice, float $total): void;

    public function supportsBillingAgreements(): bool
    {
        return false;
    }

    public function createBillingAgreement(User $user): mixed
    {
        throw new \Exception('Billing agreements not supported by this gateway.');
    }

    public function cancelBillingAgreement(BillingAgreement $billingAgreement): bool
    {
        throw new \Exception('Billing agreements not supported by this gateway.');
    }

    public function charge(Invoice $invoice, float $total, BillingAgreement $billingAgreement): void
    {
        throw new \Exception('Billing agreements not supported by this gateway.');
    }
}
