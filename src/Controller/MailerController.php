<?php
// src/Controller/MailerController.php
namespace App\Controller;

use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MailerService $mailer): Response
    {
        $to = 'habibathimenn@gmail.com';
        $subject = 'You are awesome!';
        
        $textContent = "Congrats for sending test email with Mailtrap!\n\n"
            . "If you are viewing this email in your inbox – the integration works.\n"
            . "Now send your email using our SMTP server and integration of your choice!\n\n"
            . "Good luck! Hope it works.";
            
        $htmlContent = <<<HTML
<!doctype html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body style="font-family: sans-serif;">
    <div style="display: block; margin: auto; max-width: 600px;" class="main">
      <h1 style="font-size: 18px; font-weight: bold; margin-top: 20px">Congrats for sending test email with Mailtrap!</h1>
      <p>If you are viewing this email in your inbox – the integration works.</p>
      <img alt="Inspect with Tabs" src="https://assets-examples.mailtrap.io/integration-examples/welcome.png" style="width: 100%;">
      <p>Now send your email using our SMTP server and integration of your choice!</p>
      <p>Good luck! Hope it works.</p>
    </div>
    <style>
      .main { background-color: white; }
      a:hover { border-left-width: 1em; min-height: 2em; }
    </style>
  </body>
</html>
HTML;

        $mailer->sendEmail($to, $subject, $textContent, $htmlContent);
        
        return new Response('Email sent successfully! Check your Mailtrap inbox.');
    }
}