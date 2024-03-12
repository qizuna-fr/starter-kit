<?php


namespace Domain\AuthContext\BusinessLogic\UseCases;


use Domain\AuthContext\BusinessLogic\Exceptions\InvalidCredentialsException;
use Domain\AuthContext\BusinessLogic\Gateways\AuthenticationGateway;

final class AuthenticateUser
{




    public function __construct(private AuthenticationGateway $authenticationGateway)
    {
    }

    public function __invoke(string $username, string $password){

        $response = $this->authenticationGateway->authenticate($username, $password);

        if($response === null){
            throw new InvalidCredentialsException('Invalid credentials');
        }

        return $response;

    }
}
