<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminApprovalMail extends Mailable
{
    public function __construct(public $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kasir App -> Persetujuan User Baru' 
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-approve',
            with: [
                'user' => $this->user
            ]
        );
    }
}