¿<?php
namespace App\Livewire;

use App\Models\Service;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Livewire\Component;

class TicketDetail extends Component
{
    public Ticket $ticket;

    public string $message = '';

    public $services;

    public function mount(int $ticketId)
    {
        $this->ticket = Ticket::where('id', $ticketId)
            ->where('user_id', auth()->id())
            ->with(['messages.user', 'service'])
            ->firstOrFail();

        $this->services = Service::where('user_id', auth()->id())
            ->whereIn('status', ['active', 'suspended'])
            ->get();
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required|string|min:1',
        ]);

        TicketMessage::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => auth()->id(),
            'message' => $this->message,
        ]);

        $this->ticket->update(['status' => 'open']);
        $this->message = '';
        $this->ticket->load('messages.user');
    }

    public function closeTicket()
    {
        $this->ticket->update(['status' => 'closed']);
    }

    public function render()
    {
        return view('livewire.ticket-detail');
    }
}
