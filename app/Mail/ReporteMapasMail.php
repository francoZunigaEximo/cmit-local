<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Log;

class ReporteMapasMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->data['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reporteMapas',
            with: ['content' => $this->data['content']]
        );
    }


    public function attachments(): array
    {
        $attachments = [];

        // Si existen archivos adjuntos, los agregamos al array
        if (!empty($this->data['attachments'])) {
            foreach ($this->data['attachments'] as $attachmentPath) {
                $attachments[] = Attachment::fromPath($attachmentPath);
            }
        }
        
        return $attachments;
    }
}
