<?php

declare(strict_types=1);

namespace Domain\AuthContext\Adapters\Primary\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{


    public function __construct(
        private readonly string $recaptchaSiteKey
    ) {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig',
                             [
                                 'last_username' => $lastUsername,
                                 'error' => $error,
                                 'recaptcha_site_key' => $this->recaptchaSiteKey,
                             ]
        );
    }

    // @codeCoverageIgnoreStart
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
    // @codeCoverageIgnoreEnd
}
