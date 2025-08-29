<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;

class RecaptchaAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private $httpClient;
    private $recaptchaSecret;
    private $csrfTokenManager;
    private $urlGenerator;
    private $security;

    public function __construct(
        HttpClientInterface $httpClient, 
        string $recaptchaSecret,
        CsrfTokenManagerInterface $csrfTokenManager,
        UrlGeneratorInterface $urlGenerator,
        Security $security
    ) {
        $this->httpClient = $httpClient;
        $this->recaptchaSecret = $recaptchaSecret;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST') && $request->attributes->get('_route') === 'app_login';
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('motdepasse');

        if (!$email || !$password) {
            throw new AuthenticationException('Email et mot de passe sont requis.');
        }

        // Validate CSRF token
        $csrfToken = $request->request->get('_csrf_token');
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
            throw new AuthenticationException('Token CSRF invalide.');
        }

        // Temporarily disable reCAPTCHA for testing
        // $recaptchaToken = $request->request->get('recaptcha_token');
        // 
        // if (!$recaptchaToken) {
        //     throw new AuthenticationException('Veuillez compléter le reCAPTCHA pour vous connecter.');
        // }
        // 
        // // Verify reCAPTCHA token
        // $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
        //     'body' => [
        //         'secret' => $this->recaptchaSecret,
        //         'response' => $recaptchaToken,
        //     ],
        // ]);
        // 
        // $result = json_decode($response->getContent(), true);
        // 
        // if (!$result['success']) {
        //     throw new AuthenticationException('La vérification reCAPTCHA a échoué. Veuillez réessayer.');
        // }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        
        // Check if user is verified (temporarily disabled for development)
        // if (!$user->isVerified()) {
        //     // For now, allow unverified users to proceed
        //     // In production, you should implement proper email verification
        // }
        
        // If user is admin, redirect to users list
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_users'));
        }
        
        // For all other users, redirect to profile
        return new RedirectResponse($this->urlGenerator->generate('app_profile'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->getFlashBag()->add('error', $exception->getMessage());
        }
        
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        // If user is already authenticated, redirect to appropriate page
        if ($this->security->getUser()) {
            $user = $this->security->getUser();
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return new RedirectResponse($this->urlGenerator->generate('app_users'));
            }
            return new RedirectResponse($this->urlGenerator->generate('app_profile'));
        }

        // If not authenticated, redirect to login
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
} 