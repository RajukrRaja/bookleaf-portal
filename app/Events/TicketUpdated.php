<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tickets.' . $this->ticket->id)];
    }

    public function broadcastAs(): string
    {
        return 'ticket.updated';
    }

    // No private ticket details in the Pusher payload; the authorized client fetches them.
    public function broadcastWith(): array
    {
        return ['ticket_id' => $this->ticket->id];
    }
}
