<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromocionResultado extends Mailable
{
    use Queueable, SerializesModels;

    public $promocion;
    public $vendedor;
    public $aprobada;

    /**
     * Create a new message instance.
     */
    public function __construct($promocion, $vendedor, $aprobada)
    {
        $this->promocion = $promocion;
        $this->vendedor = $vendedor;
        $this->aprobada = $aprobada;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $estado = $this->aprobada ? 'Aprobada' : 'Rechazada';
        return new Envelope(
            subject: "Resultado de tu postulación de promoción: {$estado} - " . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.promocion.resultado',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
