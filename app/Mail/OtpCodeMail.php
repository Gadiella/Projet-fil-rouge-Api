<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $username; // Ajouter la propriété username
    public $password;


    /**
     * Crée une nouvelle instance de message.
     *
     * @param string|null $username
     * @param string|null $password

     */
    public function __construct($username = null, $password = null)
    {
        $this->username = $username; // Initialiser username
        $this->password = $password;
       
    }

    /**
     * Définir l'enveloppe du message (sujet, adresse expéditeur).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Otp Code Mail', // Sujet de l'email
            from: new Address('accounts@unetah.net', 'Archiva_Nexus'), // Adresse expéditeur
        );
    }

    /**
     * Définir le contenu du message (vue et variables).
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.message', // Vue à utiliser pour l'email
            with: [ // Variables passées à la vue
                'username' => $this->username, // Inclure username
                'password' => $this->password,
               
            ]
        );
    }

    /**
     * Ajouter des pièces jointes si nécessaire.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
    
}
