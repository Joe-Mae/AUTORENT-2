<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email de test envoyé depuis les réglages pour valider le SMTP
 * configuré par le Super Admin (envoi synchrone : retour immédiat).
 */
class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly string $companyName) {}

    public function build(): static
    {
        return $this->subject(__('site.admin.settings.test_email_subject'))
            ->text('emails.mail-settings-test');
    }
}
