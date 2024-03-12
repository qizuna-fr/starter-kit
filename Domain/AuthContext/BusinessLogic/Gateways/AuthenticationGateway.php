<?php

namespace Domain\AuthContext\BusinessLogic\Gateways;

use Domain\AuthContext\BusinessLogic\Entities\User;

interface AuthenticationGateway
{
    public function authenticate(string $username, string $password): ?User;
}
