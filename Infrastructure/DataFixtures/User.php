<?php

declare(strict_types=1);

namespace Infrastructure\DataFixtures;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class User extends Fixture implements DependentFixtureInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->generateUser('user'));
        $manager->persist($this->generateUser('admin', true, ["ROLE_ADMIN"]));

        $manager->flush();
    }

    private function generateUser(string $username, bool $isActive = true, array $roles = []): \Infrastructure\Entities\User
    {
        $user = new \Infrastructure\Entities\User();
        $user->setUsername($username);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $user->setEmail("{$username}@email.fr");
        $user->setRoles($roles);
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setTenant($this->getReference('tenant-1'));

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
