<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\UserContext\Adapters\Primary\Controllers;


use DateTimeImmutable;
use Domain\AuthContext\Adapters\Secondary\Repositories\UserRepository;
use Infrastructure\Entities\User;
use Infrastructure\Form\User\CreatePasswordType;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class PasswordController extends AbstractController
{


    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route('/email/validate/{token}', name: 'app_email_validate')]
    public function validateEmail(Request $request, string $token)
    {
        try {
            //dd('validateEmail');
            $user = $this->checkToken($token);


            return $this->redirectToRoute('app_define_password', ['token' => $token]);
        } catch (InvalidArgumentException $e) {
            return $this->createNotFoundException($e->getMessage());
        }
    }

    #[Route('/password/register/{token}', name: 'app_define_password')]
    public function definePassword(Request $request, string $token)
    {
        $user = $this->checkToken($token);

        if($user === null){
            return $this->createNotFoundException('Token inexistant');
        }

        $form = $this->createForm(CreatePasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('newPassword')->getData();
            $password = $this->passwordHasher->hashPassword($user, $plainPassword);

            $user->setPassword($password);
            $user->setActivationToken(null);
            $user->setActivatedAt(new DateTimeImmutable());

            $this->userRepository->save($user, true);

            $this->addFlash('success', 'Votre mot de passe a bien été défini. Vous pouvez maintenant vous connecter.');

            return $this->redirectToRoute('app_login');

        }

        return $this->render('password/define_password.html.twig' , [
            'form' => $form->createView()
        ]);

    }

    private function checkToken(?string $token = null): ?User
    {
        if (null === $token) {
            throw new InvalidArgumentException('Token inexistant');
        }

        $user = $this->userRepository->findOneBy(['activationToken' => $token]);
        if (null === $user) {
            throw new InvalidArgumentException('Token inexistant');
        }

        return $user;
    }
}
