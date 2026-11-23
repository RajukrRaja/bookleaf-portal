<?php

use App\Models\Ticket;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('tickets.{ticketId}', function ($user, int $ticketId): bool {
    $ticket = Ticket::find($ticketId);
    return $ticket !== null && ($user->role === 'admin' || (int) $ticket->author_id === (int) $user->id);
});
