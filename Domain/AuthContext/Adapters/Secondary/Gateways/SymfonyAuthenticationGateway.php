<?php


namespace Domain\AuthContext\Adapters\Secondary\Gateways;


use Domain\AuthContext\BusinessLogic\Entities\User;
use Domain\AuthContext\BusinessLogic\Gateways\AuthenticationGateway;

final class SymfonyAuthenticationGateway implements AuthenticationGateway
{

    public function authenticate(string $username, string $password): ?User
    {
        return null;
    }
}
