<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketController extends Controller
{
    /**
     * Get all tickets for the authenticated user.
     */
    public function index(Request $request)
    {
        $tickets = Ticket::where('user_id', $request->user()->id)
            ->with(['messages' => function($query) {
                $query->latest()->take(1); // just to show last reply date
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($tickets);
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
        ]);

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'subject' => $request->subject,
            'priority' => $request->priority ?? 'normal',
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket créé avec succès.',
            'ticket' => $ticket->load('messages.user')
        ], 201);
    }

    /**
     * Display the specified ticket with all messages.
     */
    public function show(Request $request, $id)
    {
        $ticket = Ticket::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('messages.user')
            ->firstOrFail();

        return response()->json($ticket);
    }

    /**
     * Reply to a ticket.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($ticket->status === 'closed') {
            return response()->json(['message' => 'Ce ticket est fermé.'], 403);
        }

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        $ticket->update(['status' => 'open']); // Re-open if it was resolved

        return response()->json(['success' => true, 'message' => 'Réponse envoyée.']);
    }

    // ==========================================
    // ADMIN METHODS
    // ==========================================

    /**
     * Get all tickets (Admin).
     */
    public function adminIndex(Request $request)
    {
        $tickets = Ticket::with('user')->orderBy('updated_at', 'desc')->get();
        return response()->json($tickets);
    }

    /**
     * Show ticket (Admin).
     */
    public function adminShow($id)
    {
        $ticket = Ticket::with('messages.user', 'user')->findOrFail($id);
        return response()->json($ticket);
    }

    /**
     * Reply to ticket (Admin).
     */
    public function adminReply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($id);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        $ticket->update(['status' => 'answered']); // Admin answered

        return response()->json(['success' => true, 'message' => 'Réponse envoyée au client.']);
    }

    /**
     * Change ticket status (Admin).
     */
    public function adminUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,answered,resolved,closed',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour.']);
    }
}
