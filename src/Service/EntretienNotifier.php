<?php
// src/Service/EntretienNotifier.php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EntretienNotifier
{
    private MailerInterface $mailer;
    private string $environment;

    public function __construct(MailerInterface $mailer, string $environment)
    {
        $this->mailer = $mailer;
        $this->environment = $environment;
    }

    public function sendEntretienConfirmation(string $toEmail, array $entretienDetails): void
    {
        $subject = "Confirmation de votre rendez-vous d'entretien";
        
        // Email pour le client
        $clientContent = sprintf(
            "Bonjour,\n\n" .
            "Votre %s a été programmé avec succès.\n\n" .
            "Détails :\n" .
            "Date: %s\n" .
            "Type: %s\n" .
            "Coût estimé: %.2f€\n\n" .
            "Cordialement,\n" .
            "L'équipe du garage",
            $entretienDetails['type'],
            $entretienDetails['date'],
            $entretienDetails['type'],
            $entretienDetails['cout']
        );
    
        // Email pour vous (plus détaillé ou différent)
        $yourContent = sprintf(
            "Nouvel entretien créé pour le client : %s\n\n" .
            "Détails :\n" .
            "Date: %s\n" .
            "Type: %s\n" .
            "Coût estimé: %.2f€\n\n" .
            "Action requise : Vérifier les disponibilités.",
            $toEmail,
            $entretienDetails['date'],
            $entretienDetails['type'],
            $entretienDetails['cout']
        );
    
        if ($this->environment === 'prod') {
            file_put_contents(
                __DIR__.'/../../var/log/mail.log',
                sprintf(
                    "Email to client: %s\nSubject: %s\nContent: %s\n\n" .
                    "Email to admin: abdrattraore0@gmail.com\nSubject: %s\nContent: %s\n\n",
                    $toEmail,
                    $subject,
                    $clientContent,
                    "Nouvel entretien créé - " . $entretienDetails['type'],
                    $yourContent
                ),
                FILE_APPEND
            );


            
        } else {
            // Email pour le client
            $clientEmail = (new Email())
                ->from($_ENV['MAILER_FROM'])
                ->to($toEmail)
                ->subject($subject)
                ->text($clientContent);
    
            // Email pour vous
            $yourEmail = (new Email())
                ->from($_ENV['MAILER_FROM'])
                ->to('abdratraore0@gmail.com')
                ->subject("Nouvel entretien créé - " . $entretienDetails['type'])
                ->text($yourContent);
    
            $this->mailer->send($clientEmail);
            $this->mailer->send($yourEmail);
        }
    }
}