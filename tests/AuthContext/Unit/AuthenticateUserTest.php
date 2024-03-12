<?php

use Domain\AuthContext\Adapters\Secondary\Gateways\FakeAuthenticationGateway;
use Domain\AuthContext\BusinessLogic\Entities\User;
use Domain\AuthContext\BusinessLogic\Exceptions\InvalidCredentialsException;
use Domain\AuthContext\BusinessLogic\UseCases\AuthenticateUser;

beforeEach(function () {
    $this->authenticationGateway = new FakeAuthenticationGateway();
});

it('should authenticate a user', function () {
    $useCase = new AuthenticateUser($this->authenticationGateway);
    $result = $useCase('admin', 'password');
    expect($result)->toBeInstanceOf(User::class);
});

it('should throw an exception if user is unkonwn ', function () {
    $useCase = new AuthenticateUser($this->authenticationGateway);
    $useCase('unknown', 'password');
})->throws(InvalidCredentialsException::class);


it('should throw an exception if password is false', function () {
    $useCase = new AuthenticateUser($this->authenticationGateway);
    $useCase('admin', 'error');
})->throws(InvalidCredentialsException::class);
