<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'service', 'assignedTo'])
            ->latest()
            ->paginate(20);

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'service', 'assignedTo', 'messages.user']);
        $users = User::all();

        return view('admin.tickets.show', compact('ticket', 'users'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        if ($ticket->status === 'resolved') {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Reply sent successfully.');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket->update(['assigned_to' => $request->assigned_to]);

        return back()->with('success', 'Ticket assigned successfully.');
    }

    public function close(Ticket $ticket)
    {
        $ticket->update(['status' => 'resolved']);

        return back()->with('success', 'Ticket closed successfully.');
    }

    public function reopen(Ticket $ticket)
    {
        $ticket->update(['status' => 'open']);

        return back()->with('success', 'Ticket reopened.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->messages()->delete();
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}
