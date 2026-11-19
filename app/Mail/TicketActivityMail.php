<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketActivityMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $activity,
        public array $details,
        public string $recipientRole,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[BookLeaf] ' . $this->activity . ' — ' . ($this->details['ticket_number'] ?? 'Support ticket'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-activity',
            with: [
                'activity' => $this->activity,
                'details' => $this->details,
                'recipientRole' => $this->recipientRole,
            ],
        );
    }
}
