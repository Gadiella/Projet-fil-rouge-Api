<?php 

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email; // Ajouter la propriété username
// Token pour réinitialiser le mot de passe
public $token; // Email de l'utilisateur

    /**
     * Crée une nouvelle instance de message.
     *
     * @param string|null $email
     * @param string|null $token
     
     */
    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Définir l'enveloppe du message (sujet, adresse expéditeur).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'votre mot de passe', // Sujet de l'email
            from: new Address('accounts@unetah.net', 'Archiva_Nexus'), // Adresse expéditeur
        );
    }

    /**
     * Définir le contenu du message (vue et variables).
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.passwordResetSuccess',
            with: [
                'token' => $this->token,
                        'email' => $this->email,
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
