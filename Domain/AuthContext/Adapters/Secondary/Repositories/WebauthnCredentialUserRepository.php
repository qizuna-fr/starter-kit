<?php

namespace Domain\AuthContext\Adapters\Secondary\Repositories;


use Infrastructure\Entities\User;
use LogicException;
use Random\RandomException;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Connection;
use ParagonIE\ConstantTime\Base64UrlSafe;
use Webauthn\{
    Exception\InvalidDataException,
    Bundle\Repository\CanGenerateUserEntity,
    Bundle\Repository\CanRegisterUserEntity,
    PublicKeyCredentialUserEntity,
    Bundle\Repository\PublicKeyCredentialUserEntityRepositoryInterface
};

final readonly class WebauthnCredentialUserRepository implements
    PublicKeyCredentialUserEntityRepositoryInterface,
    CanRegisterUserEntity,
    CanGenerateUserEntity
{
    public function __construct(
        private UserRepository $userRepository,
        private Connection $connection
    ) {
    }

    /**
     * @see https://dba.stackexchange.com/q/253090
     * @see https://dba.stackexchange.com/a/253098
     * @todo using UUIDs would be a better idea as they are decoupled from the database
     */
    public function generateNextUserEntityId(): string
    {
        return (string) $this->connection
            ->executeQuery('SELECT last_value + 1 FROM user_id_seq;')
            ->fetchOne();
    }

    public function saveUserEntity(PublicKeyCredentialUserEntity $userEntity): void
    {
        /** @var User|null $user */
        $user = $this->userRepository->findOneBy(['id' => $userEntity->id]);

        if ($user === null) {
            $user = (new User())
                ->setEmail($userEntity->name)
                ->setRoles(['ROLE_USER']);
        }

        $this->userRepository->save($user);
    }

    public function findOneByUsername(string $username): ?PublicKeyCredentialUserEntity
    {
        $user = $this->userRepository->findOneBy(['email' => $username]);
        return $this->getUserEntity($user);
    }

    public function findOneByUserHandle(string $userHandle): ?PublicKeyCredentialUserEntity
    {
        $user = $this->userRepository->findOneBy(['id' => $userHandle]);
        return $this->getUserEntity($user);
    }

    public function generateUserEntity(?string $username, ?string $displayName): PublicKeyCredentialUserEntity
    {
        $randomUserData = Base64UrlSafe::encodeUnpadded(random_bytes(32));

        return PublicKeyCredentialUserEntity::create(
            $username ?? $randomUserData,
            $this->generateNextUserEntityId(),
            $displayName ?? $username ?? $randomUserData,
            null
        );
    }

    private function getUserEntity(null|User $user): ?PublicKeyCredentialUserEntity
    {
        if ($user === null) {
            return null;
        }

        return new PublicKeyCredentialUserEntity(
            $user->getUserIdentifier(),
            (string) $user->getId(),
            $user->getDisplayName(),
            null
        );
    }
}
