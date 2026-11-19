<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketNote;
use App\Services\BookLeafAiService;
use App\Services\TicketEmailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function create(Request $r)
    {
        return view('tickets.create', [
            'books' => $r->user()->books
        ]);
    }

    public function store(Request $r, BookLeafAiService $ai, TicketEmailNotificationService $emails)
    {
        $data = $r->validate([
            'book_id' => 'nullable|exists:books,id',
            'subject' => 'required|string|max:180',
            'description' => 'required|string|min:10|max:10000',
            'attachment' => 'nullable|file|max:5120'
        ]);
        if (!empty($data['book_id']) && !$r->user()->books()->whereKey($data['book_id'])->exists()) abort(403);
        $ticket = Ticket::create([
            'ticket_number' => 'BL-' . strtoupper(Str::random(7)),
            'author_id' => $r->user()->id,
            'book_id' => $data['book_id'] ?? null,
            'subject' => $data['subject'],
            'description' => $data['description']
        ]);
        if ($r->hasFile('attachment')) {
            $path = $r->file('attachment')->store('ticket-attachments', 'local');
        }
        $result = $ai->analyze($ticket->load('book'));
        $ticket->update([
            'category' => $result['category'],
            'priority' => $result['priority'],
            'priority_score' => $result['priority_score'],
            'ai_draft' => $result['reply']
        ]);
        $emails->notifyBoth($ticket->refresh(), 'New support ticket created', [
            'message' => 'A new support ticket has been submitted by the author.'
        ]);
        return redirect()->route('author.tickets')->with('success', 'Ticket submitted successfully.');
    }

    public function show(Request $r, Ticket $ticket)
    {
        if ($r->user()->role === 'author' && $ticket->author_id !== $r->user()->id) abort(403);
        return view('tickets.show', [
            'ticket' => $ticket->load([
                'author',
                'book',
                'messages.user',
                'notes.user',
                'assignee'
            ])
        ]);
    }

    public function reply(Request $r, Ticket $ticket, TicketEmailNotificationService $emails)
    {
        $data = $r->validate([
            'message' => 'required|string|min:1|max:10000'
        ]);
        if ($r->user()->role === 'author' && $ticket->author_id !== $r->user()->id) abort(403);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $r->user()->id,
            'message' => $data['message'],
            'is_admin' => $r->user()->role === 'admin'
        ]);
        $emails->notifyBoth($ticket, $r->user()->role === 'admin' ? 'Admin replied to ticket': 'Author replied to ticket', [
            'message' => $data['message']
        ]);
        return back()->with('success', 'Reply sent.');
    }

    public function update(Request $r, Ticket $ticket, TicketEmailNotificationService $emails)
    {
        $data = $r->validate([
            'status' => 'required|in:Open,In Progress,Resolved,Closed',
            'category' => 'required|in:Royalty & Payments,ISBN & Metadata Issues,Printing & Quality,Distribution & Availability,Book Status & Production Updates,General Inquiry',
            'priority' => 'required|in:Critical,High,Medium,Low',
            'ai_draft' => 'nullable|string|max:10000'
        ]);
        $old = $ticket->only(['status', 'priority', 'category']);
        $ticket->update($data + [
            'resolved_at' => in_array($data['status'], ['Resolved', 'Closed']) ? now(): null
        ]);
        foreach ([
            'status' => 'Ticket status changed',
            'priority' => 'Ticket priority changed',
            'category' => 'Ticket category changed'
        ] as $field => $label) {
            if (($old[$field] ?? null) !== ($ticket->{$field} ?? null)) {
                $emails->notifyBoth($ticket, $label, [
                    'message' => "The ticket {$field} was updated.",
                    'changed_to' => $ticket->{$field}
                ]);
            }
        }
        return back()->with('success', 'Ticket updated.');
    }

    public function regenerate(Request $request, Ticket $ticket, BookLeafAiService $ai)
    {
        $data = $request->validate([
            'tone' => [
                'required',
                \Illuminate\Validation\Rule::in(BookLeafAiService::TONES)
            ]
        ]);
        $result = $ai->analyze($ticket, $data['tone']);
        $ticket->update([
            'ai_draft' => $result['reply']
        ]);
        return back()->with('draft_tone', $data['tone'])->with('success', 'Draft updated for review.');
    }

    public function sendDraft(Request $r, Ticket $ticket, TicketEmailNotificationService $emails)
    {
        $data = $r->validate([
            'message' => 'required|string|max:10000'
        ]);
        $previousStatus = $ticket->status;
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $r->user()->id,
            'message' => $data['message'],
            'is_admin' => true
        ]);
        $ticket->update([
            'status' => 'In Progress'
        ]);
        $emails->notifyBoth($ticket, 'Admin replied to ticket', [
            'message' => $data['message']
        ]);
        if ($previousStatus !== 'In Progress') {
            $emails->notifyBoth($ticket, 'Ticket status changed', [
                'message' => 'The ticket status was updated.',
                'changed_to' => 'In Progress'
            ]);
        }
        return back()->with('success', 'Approved response sent to author.');
    }

    public function note(Request $r, Ticket $ticket)
    {
        $data = $r->validate([
            'note' => 'required|string|max:5000'
        ]);
        TicketNote::create([
            'ticket_id' => $ticket->id,
            'user_id' => $r->user()->id,
            'note' => $data['note']
        ]);
        return back()->with('success', 'Internal note added.');
    }

    public function assign(Request $r, Ticket $ticket, TicketEmailNotificationService $emails)
    {
        $changed = $ticket->assigned_to !== $r->user()->id;
        $ticket->update([
            'assigned_to' => $r->user()->id
        ]);
        if ($changed) {
            $emails->notifyBoth($ticket, 'Ticket assigned', [
                'assigned_to' => $r->user()->name,
                'message' => 'The ticket has been assigned to a support team member.'
            ]);
        }
        return back()->with('success', 'Ticket assigned to you.');
    }

    public function refresh(Request $r, Ticket $ticket)
    {
        if ($r->user()->role === 'author' && $ticket->author_id !== $r->user()->id) abort(403);
        return response()->json([
            'status' => $ticket->status,
            'messages' => $ticket->messages()->where('id', '>', max(0, $r->integer('after')))->orderBy('id')->with('user:id,name,role')->get(),
            'updated_at' => $ticket->updated_at
        ]);
    }
}
