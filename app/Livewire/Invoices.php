¿<?php
namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;

class Invoices extends Component
{
    use WithPagination;

    public string $status = '';

    public function render()
    {
        $query = Invoice::where('user_id', auth()->id())->with('items');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $invoices = $query->latest()->paginate(10);

        return view('livewire.invoices', compact('invoices'));
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function payInvoice(Invoice $invoice)
    {
        return redirect()->route('checkout.pay', ['invoice' => $invoice->id]);
    }
}
