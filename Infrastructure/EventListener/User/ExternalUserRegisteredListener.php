<?php

declare(strict_types=1);

namespace Infrastructure\EventListener\User;

use Domain\UserContext\Events\ExternalUserRegisteredEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: ExternalUserRegisteredEvent::class)]
class ExternalUserRegisteredListener
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private MailerInterface $mailer,
        private string $fromEmail,
        private string $fromName
    ) {
    }

    public function __invoke(ExternalUserRegisteredEvent $event): void
    {
        $user = $event->getUser();
        if ($user->getActivationToken() === null) {
            return;
        }

        $url = $this->urlGenerator->generate(
            'app_email_validate',
            ['token' => $user->getActivationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->subject('Validation de votre adresse email')
            ->htmlTemplate('emails/send_activation_link.html.twig')
            ->context([
                'url' => $url,
                'user_email' => $user->getEmail(),
                'username' => $user->getUsername(),
                'fullname' => $user->getFullName(),
            ]);

        $this->mailer->send($email);
    }
}
