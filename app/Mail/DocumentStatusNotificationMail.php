<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentStatusNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    // consstructor de las propiedades necesarias para el correo
    public function __construct(
        public readonly User $recipient,
        public readonly string $subjectLine,
        public readonly string $messageText,
        public readonly ?string $documentUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        //asunto
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        // Renderiza el correo en texto plano para evitar la salida HTML en el log.
        return new Content(
            text: 'mail.document-status-notification',
            with: [
                'recipientName' => $this->recipient->name,
                'messageText' => $this->messageText,
                'documentUrl' => $this->documentUrl,
            ],
        );
    }
}