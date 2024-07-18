<?php


namespace Domain\AuthContext\Adapters\Secondary\Gateways;


use Domain\AuthContext\BusinessLogic\Entities\User;
use Domain\AuthContext\BusinessLogic\Gateways\AuthenticationGateway;
use Symfony\Component\HttpFoundation\Request;

final class SymfonyAuthenticationGateway implements AuthenticationGateway
{

    private string $username;
    private string $password;

    public function authenticateFromCredentials(string $username, string $password): ?User
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }





}
