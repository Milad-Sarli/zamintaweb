<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->tickets()
            ->withCount('messages')
            ->latest()
            ->paginate(10);

        return Inertia::render('Dashboard/Tickets/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Tickets/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket = auth()->user()->tickets()->create([
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin' => false,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'تیکت شما با موفقیت ثبت شد.');
    }

    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        return Inertia::render('Dashboard/Tickets/Show', [
            'ticket' => $ticket->load(['messages.user']),
        ]);
    }

    public function reply(Request $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin' => false,
        ]);

        $ticket->update(['status' => 'pending_admin']);

        return back()->with('success', 'پیام شما با موفقیت ارسال شد.');
    }
}
