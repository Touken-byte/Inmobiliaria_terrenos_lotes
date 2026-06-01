<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlquilerResultado extends Mailable
{
    use Queueable, SerializesModels;

    public $alquiler;
    public $vendedor;
    public $aprobado;

    /**
     * Create a new message instance.
     */
    public function __construct($alquiler, $vendedor, $aprobado)
    {
        $this->alquiler = $alquiler;
        $this->vendedor = $vendedor;
        $this->aprobado = $aprobado;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $estado = $this->aprobado ? 'Aprobado' : 'Rechazado';
        return new Envelope(
            subject: "Resultado de tu publicación de alquiler: {$estado} - " . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.alquiler.resultado',
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
