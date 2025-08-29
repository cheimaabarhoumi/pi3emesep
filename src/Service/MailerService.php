<?php
// src/Service/MailerService.php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;
    private $from;

    public function __construct(MailerInterface $mailer, string $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function sendEmail(
        string $to,
        string $subject,
        string $textContent,
        string $htmlContent
    ): void {
        $email = (new Email())
            ->from($this->from)
            ->to($to)
            ->subject($subject)
            ->text($textContent)
            ->html($htmlContent);

        $this->mailer->send($email);
    }
}