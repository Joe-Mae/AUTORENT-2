<?php

namespace App\Support;

/**
 * Configuration SMTP pilotée depuis le back-office (§6.3).
 *
 * Les valeurs des settings écrasent celles du .env à l'exécution ;
 * un champ vide = on garde la configuration du fichier. Appliqué à
 * chaque requête HTTP et avant chaque job de la file (voir
 * AppServiceProvider) : l'entreprise change d'email sans toucher au
 * déploiement.
 */
class MailSettings
{
    public function apply(): void
    {
        try {
            $host = setting('mail_host');
        } catch (\Throwable) {
            // Base pas encore prête (installation, migrations, tests) :
            // la configuration .env reste utilisée telle quelle.
            return;
        }

        if (empty($host)) {
            return; // pas de réglage en base : configuration .env par défaut
        }

        config([
            // L'hôte étant défini, le transport par défaut devient SMTP
            // quel que soit le MAIL_MAILER du .env (log en développement).
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => (int) setting('mail_port', '587'),
            'mail.mailers.smtp.username' => setting('mail_username'),
            'mail.mailers.smtp.password' => setting('mail_password'),
            // « auto » : STARTTLS proposé par le serveur ; « smtps » : TLS implicite (port 465).
            'mail.mailers.smtp.scheme' => setting('mail_encryption', 'auto') === 'smtps' ? 'smtps' : 'smtp',
            'mail.from.address' => setting('mail_from_address') ?: config('mail.from.address'),
            'mail.from.name' => setting('mail_from_name') ?: config('mail.from.name'),
        ]);
    }

    /**
     * Destinataire des notifications internes (nouvelles demandes,
     * messages de contact) : email de notification dédié, à défaut
     * l'email de contact de l'entreprise.
     */
    public function notificationRecipient(): ?string
    {
        return setting('notify_email') ?: setting('contact_email');
    }
}
