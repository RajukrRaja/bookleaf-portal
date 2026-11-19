<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Services\TicketEmailNotificationService;
use App\Models\Ticket;
use App\Models\TicketMessage;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tickets', fn() => request()->user()->role === 'admin' ? Ticket::with(['author', 'book'])->latest()->paginate(): request()->user()->tickets()->with(['book', 'messages'])->latest()->paginate()->through(fn($ticket) => $ticket->makeHidden('ai_draft')));
    Route::post('/tickets', function(Request $r) {
        abort_unless($r->user()->role === 'author', 403);
        $d = $r->validate([
            'book_id' => 'nullable|exists:books,id',
            'subject' => 'required|string|max:180',
            'description' => 'required|string|min:10|max:10000'
        ]);
        if (!empty($d['book_id'])) abort_unless($r->user()->books()->whereKey($d['book_id'])->exists(), 403);
        $t = Ticket::create([
            'ticket_number' => 'BL-' . strtoupper(\Illuminate\Support\Str::random(7)),
            'author_id' => $r->user()->id,
            'book_id' => $d['book_id'] ?? null,
            'subject' => $d['subject'],
            'description' => $d['description']
        ]);
        $ai = app(\App\Services\BookLeafAiService::class)->analyze($t);
        $t->update([
            'category' => $ai['category'],
            'priority' => $ai['priority'],
            'priority_score' => $ai['priority_score'],
            'ai_draft' => $ai['reply']
        ]);
        app(TicketEmailNotificationService::class)->notifyBoth($t->refresh(), 'New support ticket created', [
            'message' => 'A new support ticket has been submitted by the author.'
        ]);
        return response()->json([
            'data' => $t->makeHidden('ai_draft')
        ], 201);
    });
    Route::post('/tickets/{ticket}/reply', function(Request $r, Ticket $ticket) {
        abort_if($r->user()->role === 'author' && $ticket->author_id !== $r->user()->id, 403);
        $d = $r->validate([
            'message' => 'required|string|max:10000'
        ]);
        $m = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $r->user()->id,
            'message' => $d['message'],
            'is_admin' => $r->user()->role === 'admin'
        ]);
        app(TicketEmailNotificationService::class)->notifyBoth($ticket, $r->user()->role === 'admin' ? 'Admin replied to ticket': 'Author replied to ticket', [
            'message' => $d['message']
        ]);
        return response()->json([
            'data' => $m
        ], 201);
    });
    Route::get('/tickets/{ticket}/refresh', function(Request $r, Ticket $ticket) {
        abort_if($r->user()->role === 'author' && $ticket->author_id !== $r->user()->id, 403);
        return response()->json([
            'status' => $ticket->status,
            'messages' => $ticket->messages()->with('user:id,name,role')->get(),
            'updated_at' => $ticket->updated_at
        ]);
    });
});
