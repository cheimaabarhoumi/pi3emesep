<?php
// src/Controller/TestController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('houssem.benmabrouk12@gmail.com')
            ->to('your@email.com') // Use any email, it will go to Mailtrap
            ->subject('Test Email')
            ->text('This is a test email from Symfony Mailer!');

        $mailer->send($email);

        return new Response('Test email sent!');
    }
}