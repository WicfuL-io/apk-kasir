<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class UserRejectedMail extends Mailable
{
    public function __construct(public $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kasir App -> Pendaftaran Ditolak'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-rejected',
            with: [
                'user' => $this->user
            ]
        );
    }
}