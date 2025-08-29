<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use App\Service\EmailService;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Psr\Log\LoggerInterface;

class UserController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
      
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                $this->addFlash('error', implode('<br>', $errors));
            } else {
                try {
                    $plainPassword = $form->get('motdepasse')->getData();
                    
                    if (!$plainPassword) {
                        $this->addFlash('error', 'Le mot de passe est obligatoire');
                        return $this->render('user/register.html.twig', [
                            'registrationForm' => $form->createView(),
                        ]);
                    }

                    $user->setMotdepasse(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $plainPassword
                        )
                    );

                    if (!$user->getRole()) {
                        $user->setRole('client');
                    }

                    $entityManager->persist($user);
                    $entityManager->flush();

                    // Email sending with enhanced error handling
                    try {
                        if ($emailService->sendWelcomeEmail($user->getEmail(), $user->getPrenom())) {
                            $this->addFlash('success', 'Votre compte a été créé avec succès ! Un email de bienvenue a été envoyé.');
                        } else {
                            $this->addFlash('warning', 'Compte créé mais échec d\'envoi du mail de bienvenue');
                        }
                    } catch (\Exception $emailException) {
                        $this->logger->error('Email sending failed: '.$emailException->getMessage());
                        $this->addFlash('warning', 'Compte créé mais problème d\'envoi du mail de confirmation');
                    }

                    return $this->redirectToRoute('app_login');
                } catch (\Exception $e) {
                    $this->logger->error('Registration failed: '.$e->getMessage());
                    $this->addFlash('error', 'Une erreur est survenue lors de la création du compte : '.$e->getMessage());
                }
            }
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // If user is already authenticated, redirect to appropriate page
        if ($this->getUser()) {
            $user = $this->getUser();
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('app_users');
            }
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'recaptcha_site_key' => $this->getParameter('recaptcha.site_key'),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall
        // The actual logout is handled by Symfony's security system
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('motdepasse')->getData();
            if ($plainPassword) {
                $user->setMotdepasse(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $plainPassword
                    )
                );
            }

            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/users', name: 'app_users')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('user/_users_table.html.twig', [
                'users' => $users,
            ]);
        }
        
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    

    #[Route('/user/show/{id}', name: 'app_user_show')]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/edit/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été modifié avec succès !');
            return $this->redirectToRoute('app_users');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/delete/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès !');
        }

        return $this->redirectToRoute('app_users');
    }

    #[Route('/users/pdf', name: 'app_users_pdf')]
    public function generatePdf(UserRepository $userRepository, PdfService $pdfService): Response
    {
        $users = $userRepository->findAll();
        
        $pdfContent = $pdfService->generateUsersPdf($users);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'liste-utilisateurs.pdf'
        ));

        return $response;
    }

    #[Route('/user/toggle/{id}', name: 'app_user_toggle', methods: ['POST'])]
    public function toggleUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('toggle' . $user->getId(), $token)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_users');
        }

        $user->setIsVerified(!$user->isVerified());
        $entityManager->flush();
        
        $status = $user->isVerified() ? 'activé' : 'désactivé';
        $this->addFlash('success', "L'utilisateur a été {$status} avec succès !");

        return $this->redirectToRoute('app_users');
    }

    #[Route('/users/search', name: 'app_users_search', methods: ['GET'])]
    public function ajaxSearch(Request $request, UserRepository $userRepository): Response
    {
        $query = $request->query->get('q', '');
        $role = $request->query->get('role', '');
        $sortBy = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'ASC');
    
        $users = $userRepository->searchUsers($query, $role, $sortBy, $order);
    
        return $this->render('user/_users_table.html.twig', [
            'users' => $users,
        ]);
    }
    
    

}