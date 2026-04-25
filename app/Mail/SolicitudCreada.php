<?php

namespace App\Mail;

use App\Models\SolicitudVisita;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudCreada extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;
    public $destinatarioTipo;

    /**
     * Create a new message instance.
     */
    public function __construct(SolicitudVisita $solicitud, $destinatarioTipo = 'cliente')
    {
        $this->solicitud = $solicitud;
        $this->destinatarioTipo = $destinatarioTipo; // 'cliente' o 'vendedor'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $asunto = $this->destinatarioTipo === 'vendedor' 
            ? 'Nueva solicitud de visita asignada' 
            : 'Tu solicitud de visita ha sido registrada';

        return new Envelope(
            subject: $asunto,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.solicitudes.creada',
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
