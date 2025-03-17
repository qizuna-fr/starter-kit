<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\UserContext\Adapters\Primary\Controllers;


use Domain\AuthContext\Adapters\Secondary\Repositories\UserRepository;
use Infrastructure\Form\User\UserPasswordChangeType;
use Infrastructure\Form\User\UserSettingsPersonalInformationsType;
use Infrastructure\Service\Security\TwoFactorSecurityConfig;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface as GoogleAuthenticatorTwoFactorInterface;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface as TotpTwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorTwoFactorProvider;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorTwoFactorProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

use function dd;
use function get_class;


class UserController extends AbstractController
{


    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private TwoFactorSecurityConfig $securityConfig
    ) {
    }

    #[Route('/profile', name: 'app_user_settings')]
    public function settings(Request $request)
    {
        $form = $this->createForm(UserSettingsPersonalInformationsType::class, $this->getUser());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save($form->getData(), true);
            $this->addFlash('success', 'Vos informations ont bien été mises à jour');

            return $this->redirectToRoute('app_user_settings');
        }

        return $this->render('user/settings.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

    #[Route('/profile/password', name: 'app_user_settings_password')]
    public function password(Request $request)
    {
        $form = $this->createForm(UserPasswordChangeType::class,);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $data = $form->getData();

            if (!$this->passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('app_user_settings_password');
            }

            if ($data['newPassword'] !== $data['newPasswordConfirm']) {
                $this->addFlash('error', 'Les nouveaux mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_user_settings_password');
            }

            $encodedPassword = $this->passwordHasher->hashPassword($user, $data['newPassword']);
            $user->setPassword($encodedPassword);

            $this->userRepository->save($user, true);

            return $this->redirectToRoute('app_logout');
            //return $this->redirectToRoute('app_user_settings');
        }

        return $this->render(
            'user/password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/profile/2fa', name: 'app_user_2fa')]
    public function twoFactor()
    {
        $authClass = $this->securityConfig->getProviders()[0] ?? null;

        if($authClass === null){
            return $this->redirectToRoute('app_user_settings');
        }

        $authenticator = get_class($authClass);

        $result = match (true) {
            str_ends_with(
                $authenticator,
                'GoogleAuthenticatorTwoFactorProvider'
            ) => 'GoogleAuthenticatorTwoFactorProvider',
            str_ends_with($authenticator, 'TotpAuthenticatorTwoFactorProvider') => 'TotpAuthenticatorTwoFactorProvider',
            str_ends_with($authenticator, 'EmailTwoFactorProvider') => 'EmailTwoFactorProvider',
            default => null,
        };

        //return $this->render('members/index.html.twig',);

        return $this->render(
            'user/2fa.html.twig',
            [
                'result' => $result,
                'hasGoogleSecret' => $this->getUser()->getGoogleAuthenticatorSecret() !== null,
                'hasTotpSecret' => $this->getUser()->isTotpAuthenticationEnabled()
            ]
        );

        //GoogleAuthenticatorTwoFactorProvider
        //TotpAuthenticatorTwoFactorProvider
        //EmailTwoFactorProvider

        //return $this->render('user/2fa.html.twig');
    }

    #[Route('/profile/passkeys', name: 'app_user_passkeys')]
    public function passkeys()
    {
        return $this->render(
            'user/create_passkey.html.twig',
            [

            ]
        );
    }


    #[Route('/profile/2fa/activate/totp', name: 'app_user_2fa_activate_totp')]
    public function activate2FATotp(TotpAuthenticatorInterface $totpAuthenticator )
    {
        $provider = $this->securityConfig->getProviders()[0];

        if($provider instanceof TotpAuthenticatorTwoFactorProvider ){

            $user = $this->getUser();
            $secret = $totpAuthenticator->generateSecret();
            $user->setTotpAuthenticationSecret($secret);

            $this->userRepository->save($user, true);

            return $this->redirectToRoute('app_user_2fa');
        }

        return $this->redirectToRoute('app_user_2fa');
    }

    #[Route('/profile/2fa/activate/google', name: 'app_user_2fa_activate_google')]
    public function activate2FAGoogle(GoogleAuthenticatorInterface $googleAuthenticator )
    {
        $provider = $this->securityConfig->getProviders()[0];

        if($provider instanceof GoogleAuthenticatorTwoFactorProvider ){

            $user = $this->getUser();
            $secret = $googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticatorSecret($secret);

            $this->userRepository->save($user, true);

            return $this->redirectToRoute('app_user_2fa');
        }

        return $this->redirectToRoute('app_user_2fa');

    }

    #[Route('/profile/2fa/disable/google', name: 'app_user_2fa_disable_google')]
    public function deactivate2FAGoogle(){
        $this->getUser()->setGoogleAuthenticatorSecret(null);
        $this->userRepository->save($this->getUser(), true);

        return $this->redirectToRoute('app_user_2fa');
    }

    #[Route('/profile/2fa/disable/totp', name: 'app_user_2fa_disable_totp')]
    public function deactivate2FATotp(){
        $this->getUser()->setTotpAuthenticationSecret(null);
        $this->userRepository->save($this->getUser(), true);

        return $this->redirectToRoute('app_user_2fa');
    }





    /**
     *
     * TOTP field example with convention
     *
     *
     *Please note that our development team advised, for web:

    Make sure the input is of type "text", "tel" or "number".

    Add attributes for pass to get the hints (otp, totp, onetimecode etc.. ie:

    autocomplete="one-time-code"
    id="totp"
    name="totp"
    Add a label to your input with some appropriate content without any words that we could detect as an MFA outliers (sms, email, telephone verification codes etc..).
    IE, this will always get detected:

    <div>
    <label for="totp">OTP Code</label>
    <input id="totp" autocomplete="one-time-code" required="" type="text" name="totp">
    </div>
     */


}
