<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class VigilanciaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $params;
    protected $user;

    protected $attachment;

    public function __construct($params,$user, $attachment = null)
    {
        $this->params = $params;
        $this->user = $user;
        $this->attachment = $attachment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noti_sistemas@dirislimaeste.gob.pe', 'Notificaciones Sistemas'),
            subject: 'Notiifcación VIG EPI',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {   
        $payload =  json_decode($this->params['payload']) ;

        return new Content(
            view: 'mail.vigilancia',
            with: [
                'code_customer' => $this->params['code_customer'],
                'codigo_seguimiento' => $this->params['prefix'] . '-' . $this->params['period'] . '-' . $this->params['id'],
                'payload' => $payload,
                'user' => $this->user

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
