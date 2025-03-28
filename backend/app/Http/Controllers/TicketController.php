<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Display a paginated list of tickets for the authenticated user
     */
    public function index()
    {
        $tickets = Auth::user()
            ->tickets()
            ->with('user') // Eager load user relationship
            ->latest()
            ->paginate(10); // Paginate for better performance

        return view('tickets.index', [
            'tickets' => $tickets,
            'statuses' => ['confirmed', 'pending', 'cancelled'] // For potential filtering
        ]);
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        return view('tickets.create', [
            'min_date' => now()->format('Y-m-d') // Set minimum date to today
        ]);
    }

    /**
     * Store a newly created ticket with validation and logging
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'quantity' => 'required|integer|min:1|max:10', // Added max limit
        ]);

        try {
            $ticket = Auth::user()->tickets()->create([
                'event_name' => $validated['event_name'],
                'date' => $validated['date'],
                'quantity' => $validated['quantity'],
                'status' => 'confirmed'
            ]);

            Log::info('Ticket created', ['ticket_id' => $ticket->id, 'user_id' => Auth::id()]);

            return redirect()
                ->route('tickets.index')
                ->with('success', __('Ticket purchased successfully!'));

        } catch (\Exception $e) {
            Log::error('Ticket creation failed', ['error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->with('error', __('Failed to create ticket. Please try again.'));
        }
    }

    /**
     * Display a specific ticket with authorization
     */
    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);
        
        return view('tickets.show', [
            'ticket' => $ticket->load('user'), // Eager load relationships
            'canEdit' => Gate::allows('update', $ticket) // Check edit permission
        ]);
    }

    /**
     * Show the form for editing a ticket
     */
    public function edit(Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        return view('tickets.edit', [
            'ticket' => $ticket,
            'statuses' => ['confirmed', 'pending', 'cancelled']
        ]);
    }

    /**
     * Update the specified ticket with validation
     */
    public function update(Request $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'quantity' => 'required|integer|min:1|max:10',
            'status' => 'required|in:confirmed,pending,cancelled',
        ]);

        try {
            $ticket->update($validated);
            Log::info('Ticket updated', ['ticket_id' => $ticket->id]);

            return redirect()
                ->route('tickets.index')
                ->with('success', __('Ticket updated successfully!'));

        } catch (\Exception $e) {
            Log::error('Ticket update failed', ['ticket_id' => $ticket->id, 'error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->with('error', __('Failed to update ticket. Please try again.'));
        }
    }

    /**
     * Cancel a ticket (soft delete)
     */
    public function destroy(Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);

        try {
            $ticket->update(['status' => 'cancelled']);
            Log::info('Ticket cancelled', ['ticket_id' => $ticket->id]);

            return redirect()
                ->route('tickets.index')
                ->with('success', __('Ticket cancelled successfully!'));

        } catch (\Exception $e) {
            Log::error('Ticket cancellation failed', ['ticket_id' => $ticket->id, 'error' => $e->getMessage()]);
            return back()
                ->with('error', __('Failed to cancel ticket. Please try again.'));
        }
    }
}