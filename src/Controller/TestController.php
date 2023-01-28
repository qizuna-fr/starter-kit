<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

final class TestController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('index.html.twig');
    }

//    #[Route('/login')]
//    final  public function login(): Response
//    {
//        return $this->render('login.html.twig');
//    }

    #[Route('/email')]
    public function email(MailerInterface $mailer): Response
    {
        $email = (new TemplatedEmail())
            ->from('fabien@example.com')
            ->to(new Address('ryan@example.com'))
            ->subject('Thanks for signing up down!')

            // path of the Twig template to render
            ->htmlTemplate('emails/example.html.twig')

            /** TODO : Find a way to disable cache when twig is rendering
             * Fow now, solution is to rename the file to force new rendering
             */

            // pass variables (name => value) to the template
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => 'foo',
            ])
        ;

        $mailer->send($email);

        return new Response(null, 201);
    }
}
