<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display all tickets for the authenticated user
     */
    public function index()
    {
        $tickets = Auth::user()->tickets()->latest()->get();
        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Store a newly created ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'date' => 'required|date',
            'quantity' => 'required|integer|min:1',
        ]);

        Auth::user()->tickets()->create([
            'event_name' => $validated['event_name'],
            'date' => $validated['date'],
            'quantity' => $validated['quantity'],
            'status' => 'confirmed' // Default status
        ]);

        return redirect()->route('tickets.index')
                         ->with('success', 'Ticket purchased successfully!');
    }

    /**
     * Display a specific ticket
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing a ticket
     */
    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        return view('tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified ticket
     */
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:confirmed,pending,cancelled',
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.index')
                         ->with('success', 'Ticket updated successfully!');
    }

    /**
     * Cancel/Delete a ticket
     */
    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);
        
        // Soft delete or change status instead of permanent deletion
        $ticket->update(['status' => 'cancelled']);
        // Or for permanent deletion: $ticket->delete();

        return redirect()->route('tickets.index')
                         ->with('success', 'Ticket cancelled successfully!');
    }
}