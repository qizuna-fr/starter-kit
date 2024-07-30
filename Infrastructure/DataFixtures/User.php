<?php

declare(strict_types=1);

namespace Infrastructure\DataFixtures;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function get_class;

final class User extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,

    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $tenant = $this->getReference('tenant-1');

        $manager->persist($this->generateUser('user'));
        $manager->persist($this->generateUser('admin', true, ["ROLE_LOGICIEL_ADMINISTRATEUR"]));

        $manager->persist($this->generateUser('user_client', true, ["ROLE_CLIENT_UTILISATEUR"], $tenant));
        $manager->persist($this->generateUser('admin_client', true, ["ROLE_CLIENT_ADMINISTRATEUR"], $tenant));

        $manager->flush();
    }

    private function generateUser(
        string $username,
        bool $isActive = true,
        array $roles = [],
        $tenant = null
    ): \Infrastructure\Entities\User {
        $user = new \Infrastructure\Entities\User();
        $user->setUsername($username);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $user->setEmail("{$username}@email.fr");
        $user->setRoles($roles);
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setTenant($tenant);

        if ($isActive) {
            $user->setActivatedAt(new DateTimeImmutable('2000-01-01'));
        }

        return $user;
    }


    public function getDependencies()
    {
        return [
            Tenant::class,
        ];
    }
}
