<?php

namespace App\Services;

use App\Mail\TicketActivityMail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TicketEmailNotificationService
{
    /**
     * Notify every admin and the ticket author. Email failures are logged and
     * never roll back a ticket action that has already been saved.
     */
    public function notifyBoth(Ticket $ticket, string $activity, array $extra = []): void
    {
        $ticket->loadMissing(['author', 'book', 'assignee']);

        $details = array_merge([
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'author_name' => $ticket->author?->name ?? 'Author',
            'author_email' => $ticket->author?->email ?? '',
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'category' => $ticket->category,
            'book_title' => $ticket->book?->title,
            'ticket_url' => url('/tickets/' . $ticket->id),
        ], $extra);

        if (app()->environment('local') && $inbox = config('mail.local_test_recipient')) {
            $this->send($inbox, $activity, $details, 'admin');
            return;
        }

        $author = $ticket->author;
        if ($author && filter_var($author->email, FILTER_VALIDATE_EMAIL)) {
            $this->send($author->email, $activity, $details, 'author');
        }

        User::query()->where('role', 'admin')->whereNotNull('email')->get()
            ->each(function (User $admin) use ($activity, $details) {
                if (filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
                    $this->send($admin->email, $activity, $details, 'admin');
                }
            });
    }

    private function send(string $email, string $activity, array $details, string $role): void
    {
        try {
            Mail::to($email)->send(new TicketActivityMail($activity, $details, $role));
        } catch (Throwable $e) {
            if (request()->hasSession()) {
                request()->session()->flash('mail_warning', 'Ticket saved, but an email notification failed. Please contact the support administrator.');
            }
            Log::warning('BookLeaf ticket email could not be sent.', [
                'recipient' => $email,
                'activity' => $activity,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
