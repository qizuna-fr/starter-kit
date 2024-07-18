<?php

namespace Domain\AuthContext\BusinessLogic\Repositories;

interface PasswordResetRequestRepository
{
    public function findAll();
    public function createRequest(string $username);
    public function findByUsername(string $username);
}
