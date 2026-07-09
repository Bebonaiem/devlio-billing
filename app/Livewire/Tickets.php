<?php
namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;

class Tickets extends Component
{
    public $tickets;

    protected $listeners = ['ticketCreated' => '$refresh'];

    public function mount()
    {
        $this->loadTickets();
    }

    public function loadTickets()
    {
        $this->tickets = Ticket::where('user_id', auth()->id())
            ->with('service')
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.tickets');
    }
}
