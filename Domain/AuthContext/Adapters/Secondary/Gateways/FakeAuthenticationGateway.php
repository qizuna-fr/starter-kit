<?php


namespace Domain\AuthContext\Adapters\Secondary\Gateways;


use Domain\AuthContext\BusinessLogic\Entities\User;
use Domain\AuthContext\BusinessLogic\Gateways\AuthenticationGateway;

use function array_filter;
use function convert_uudecode;

final class FakeAuthenticationGateway implements AuthenticationGateway
{
    private array $users = [];


    public function __construct()
    {
        $this->users = [new User('admin', 'password')];
    }


    public function authenticateFromCredentials(string $username, string $password): ?User
    {
        $users =  array_values(
            array_filter(
                $this->users,
                fn(User $user) => $user->getUsername() === $username && $user->getPassword() === $password
            )
        );

        return  $users[0] ?? null;
    }
}
