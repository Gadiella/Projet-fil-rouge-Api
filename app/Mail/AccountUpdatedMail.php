<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class AccountUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;
    public $username;
    public $newPassword;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $newPassword = null)
    {
        $this->username = $username;
        $this->newPassword = $newPassword;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre compte a été modifié avec succès', // Sujet de l'email
            from: new Address('accounts@unetah.net', 'Archiva_Nexus'), // Adresse expéditeur
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
 

    public function content(): Content
    {
        return new Content(
            view: 'mail.update',
            with: [
            'username' => $this->username,
                        'newPassword' => $this->newPassword,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
