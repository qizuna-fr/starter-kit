<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Infrastructure\EventListener\Admin;


use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Infrastructure\Entities\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener]
class UserRegisteredListener
{


    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private MailerInterface $mailer,
        private string $fromEmail,
        private string $fromName
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
                ->from(new Address($this->fromEmail, $this->fromName))
                ->subject('Validation de votre adresse email')
                ->htmlTemplate('emails/send_activation_link.html.twig')
                ->context(
                    [
                        'url' => $url,
                        'user_email' => $entity->getEmail(),
                        'username' => $entity->getUsername(),
                        'fullname' => $entity->getFullName()
                    ]
                );

            $this->mailer->send($email);
        }
    }
}
