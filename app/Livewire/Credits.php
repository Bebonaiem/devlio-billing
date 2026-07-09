¿<?php
namespace App\Livewire;

use App\Models\Credit;
use App\Models\Currency;
use App\Services\InvoiceService;
use Livewire\Component;

class Credits extends Component
{
    public $balance = 0;

    public string $currencyCode = 'USD';

    public float $depositAmount = 0;

    public $currencies;

    public function mount()
    {
        $this->currencyCode = session('currency', config('settings.default_currency', 'USD'));
        $this->currencies = Currency::where('enabled', true)->get();
        $this->loadBalance();
    }

    public function loadBalance()
    {
        $credit = Credit::where('user_id', auth()->id())
            ->where('currency_code', $this->currencyCode)
            ->first();

        $this->balance = $credit ? (float) $credit->amount : 0;
    }

    public function updatedCurrencyCode()
    {
        $this->loadBalance();
    }

    public function deposit()
    {
        if ($this->depositAmount < 1) {
            return;
        }

        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->createInvoice(auth()->user(), [
            [
                'quantity' => 1,
                'price' => $this->depositAmount,
                'description' => "Credit Deposit ({$this->currencyCode} ".number_format($this->depositAmount, 2).')',
            ],
        ], $this->currencyCode);

        return redirect()->route('checkout.pay', ['invoice' => $invoice->id]);
    }

    public function render()
    {
        return view('livewire.credits');
    }
}
