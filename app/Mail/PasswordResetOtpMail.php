<?php

namespace App\Mail;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;
    public $otpCode;
    public $username;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $otpCode)
    {
        $this->username = $username;
        $this->otpCode = $otpCode;
    }
    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Votre compte a été supprimé', // Sujet de l'email
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
            view: 'mail.passwordResetOtp', // Vue qui sera utilisée
            with: [
                'username' => $this->username,
                'otpCode' => $this->otpCode,
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
