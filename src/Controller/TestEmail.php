<?php
// src/Controller/TestEmailController.php
namespace App\Controller;

use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestEmail extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerService $mailer): Response
    {
        $mailer->sendEmail(
            'habibathimenn@gmail.com',
            'Test Email',
            'This is a plain text test email',
            '<p>This is an <strong>HTML</strong> test email</p>'
        );
        
        return new Response('Email sent! Check Mailtrap inbox.');
    }
}