<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Usuario;
use App\Models\HistorialVerificacion;

class ResultadoVerificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $historial;

    /**
     * Create a new message instance.
     */
    public function __construct(Usuario $usuario, HistorialVerificacion $historial)
    {
        $this->usuario = $usuario;
        $this->historial = $historial;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $estado = ucfirst($this->historial->accion);
        return new Envelope(
            subject: "Resultado de Verificación de Identidad: {$estado}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.resultado_verificacion',
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
