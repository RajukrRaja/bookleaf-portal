<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('bookleaf:mail-test {email}', function () {
    $email = $this->argument('email');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Enter a valid recipient email address.');
        return 1;
    }
    if (config('mail.default') !== 'smtp') {
        $this->error('Set MAIL_MAILER=smtp in .env and run php artisan config:clear.');
        return 1;
    }

    try {
        Mail::raw('Your BookLeaf SMTP test reached this mailbox.', function ($message) use ($email) {
            $message->to($email)->subject('BookLeaf SMTP test');
        });
    } catch (Throwable $e) {
        $this->error('SMTP test failed. '.$e->getMessage());
        return 1;
    }
    $this->info('SMTP accepted the message. Check the recipient inbox and spam folder.');
    return 0;
})->purpose('Test the active SMTP configuration');
