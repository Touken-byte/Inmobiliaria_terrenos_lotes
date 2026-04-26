<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TerrenoRechazado extends Mailable
{
    use Queueable, SerializesModels;

    public $terreno;
    public $vendedor;
    public $motivo;

    /**
     * Create a new message instance.
     */
    public function __construct($terreno, $vendedor, $motivo)
    {
        $this->terreno = $terreno;
        $this->vendedor = $vendedor;
        $this->motivo = $motivo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Atención: Tu publicación requiere correcciones - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.terreno.rejection',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
