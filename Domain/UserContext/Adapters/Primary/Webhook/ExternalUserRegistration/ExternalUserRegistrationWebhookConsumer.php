<?php

declare(strict_types=1);

/** Qizuna 2025 - tous droits reservés  **/

namespace Domain\UserContext\Adapters\Primary\Webhook\ExternalUserRegistration;

use DateTimeImmutable;
use Domain\AuthContext\Adapters\Secondary\Repositories\UserRepository;
use Domain\UserContext\Events\ExternalUserRegisteredEvent;
use Infrastructure\Entities\User;
use Infrastructure\EventListener\Admin\UserRegisteredListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

use Symfony\Component\Uid\Uuid;

use function bin2hex;

#[AsRemoteEventConsumer('external_user_registration')]
class ExternalUserRegistrationWebhookConsumer implements ConsumerInterface{




    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function consume(RemoteEvent $event): void
    {
        //do stuff for user registration
        $payload = $event->getPayload();

        $user = new User();
        $user->setUsername($payload['username']);
        $user->setEmail($payload['username']);
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setRoles(['ROLE_USER']);
        $user->setFirstName($payload['firstname']);
        $user->setLastName($payload['lastname']);
        $user->setActivationToken(bin2hex(random_bytes(32)));
        $user->setUuid(Uuid::v4()->toRfc4122());

        $this->userRepository->save($user, true);
        $this->eventDispatcher->dispatch(new ExternalUserRegisteredEvent($user));


    }


}
