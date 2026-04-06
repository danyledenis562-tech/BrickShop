<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{name: string, quantity: int}>  $lines
     */
    public function __construct(
        public User $user,
        public array $lines,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('messages.abandoned_cart_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.abandoned-cart',
        );
    }
}
