<?php


namespace Domain\AuthContext\BusinessLogic\UseCases;


use Domain\AuthContext\BusinessLogic\Repositories\PasswordResetRequestRepository;

final class InitPasswordResetRequest
{


    public function __construct(
        private PasswordResetRequestRepository $passwordResetRequestRepository
    )
    {
    }

    public function __invoke(string $username)
    {

        if($this->passwordResetRequestRepository->findByUsername($username) !== null){
            return;
        }

        $this->passwordResetRequestRepository->createRequest($username);
    }

}
