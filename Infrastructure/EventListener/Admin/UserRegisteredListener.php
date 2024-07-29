<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Infrastructure\EventListener\Admin;


use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Infrastructure\Entities\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener]
class UserRegisteredListener
{


    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private MailerInterface $mailer
    ) {
    }

    public function __invoke(AfterEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof User && $entity->getActivationToken() !== null) {
            $url = $this->urlGenerator
                ->generate(
                    'app_email_validate',
                    ['token' => $entity->getActivationToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            // Send email with $url

            $email = (new TemplatedEmail())
                ->to($entity->getEmail())
                ->from('systeme@qizuna.fr')
                ->subject('Validation de votre adresse email')
                ->htmlTemplate('emails/example.html.twig')
                ->context([
                              'url' => $url,
                          ]);

            $this->mailer->send($email);
        }
    }
}
