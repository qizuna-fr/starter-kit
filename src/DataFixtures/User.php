<?php

declare(strict_types=1);

namespace App\DataFixtures;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class User extends Fixture
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

    private function generateUser(string $username, bool $isActive = true, array $roles = []): \App\Entity\User
    {
        $user = new \App\Entity\User();
        $user->setUsername($username);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $user->setEmail("{$username}@email.fr");
        $user->setRoles($roles);
        $user->setCreatedAt(new DateTimeImmutable());

        if ($isActive) {
            $user->setActivatedAt(new DateTimeImmutable('2000-01-01'));
        }

        return $user;
    }
}
