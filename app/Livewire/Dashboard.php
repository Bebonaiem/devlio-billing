¿<?php
namespace App\Livewire;

use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Models\Service;
use App\Models\Ticket;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        $stats = [
            'active_services' => Service::where('user_id', $user->id)->where('status', 'active')->count(),
            'pending_invoices' => Invoice::where('user_id', $user->id)->where('status', 'pending')->count(),
            'open_tickets' => Ticket::where('user_id', $user->id)->whereIn('status', ['open', 'replied'])->count(),
            'total_spent' => InvoiceTransaction::where('status', 'succeeded')
                ->whereHas('invoice', fn ($q) => $q->where('user_id', $user->id))
                ->sum('amount'),
        ];

        $recentServices = Service::where('user_id', $user->id)
            ->with(['product', 'plan', 'server'])
            ->latest()
            ->take(5)
            ->get();

        $recentInvoices = Invoice::where('user_id', $user->id)
            ->with('items')
            ->latest()
            ->take(5)
            ->get();

        $activity = collect();

        foreach ($recentServices as $service) {
            $activity->push([
                'type' => 'service',
                'title' => $service->label,
                'status' => $service->status,
                'date' => $service->created_at,
                'url' => route('dashboard.service-detail', $service->id),
            ]);
        }

        foreach ($recentInvoices as $invoice) {
            $activity->push([
                'type' => 'invoice',
                'title' => 'Invoice '.$invoice->number.' - '.$invoice->formatted_total,
                'status' => $invoice->status,
                'date' => $invoice->created_at,
                'url' => route('dashboard.invoice-detail', $invoice->number),
            ]);
        }

        $activity = $activity->sortByDesc('date')->take(10);

        return view('livewire.dashboard', compact('user', 'stats', 'recentServices', 'recentInvoices', 'activity'));
    }
}
