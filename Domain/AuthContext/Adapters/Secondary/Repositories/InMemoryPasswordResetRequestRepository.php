<?php


namespace Domain\AuthContext\Adapters\Secondary\Repositories;


use Domain\AuthContext\BusinessLogic\Repositories\PasswordResetRequestRepository;

use function array_values;

final class InMemoryPasswordResetRequestRepository implements PasswordResetRequestRepository
{
    private array $passwordResetRequests = [];

    public function findAll()
    {
        return $this->passwordResetRequests;
    }

    public function createRequest(string $username)
    {
        $this->passwordResetRequests[] = $username;
    }

    public function findByUsername(string $username)
    {
        return array_values(
            array_filter($this->passwordResetRequests, fn($request) => $request === $username)
        )[0] ?? null;
    }
}
