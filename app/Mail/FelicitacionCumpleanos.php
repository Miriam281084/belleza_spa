<?php

namespace App\Mail;

use App\Models\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FelicitacionCumpleanos extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $cliente;

    /**
     * Create a new message instance.
     */
    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Feliz Cumpleaños! - Belleza & SPA Victoria',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.felicitacion-cumpleanos',
            with: [
                'cliente' => $this->cliente,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
