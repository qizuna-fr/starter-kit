<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Infrastructure\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserPasswordChangeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'currentPassword',
            PasswordType::class,
            [
                'toggle' => true,
                'hidden_label' => 'Masquer',
                'visible_label' => 'Afficher',
               'label' => 'Mot de passe actuel',
                'attr' => [
                    'class' => 'app__form_input mb-4'
                ],
            ]
        )
            ->add(
                'newPassword',
                PasswordType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Regex([
                                             'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                                             'message' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.'
                                         ]),
                    ],
                    'toggle' => true,
                    'hidden_label' => 'Masquer',
                    'visible_label' => 'Afficher',
                    'label' => 'Nouveau mot de passe',
                    'attr' => [
                        'class' => 'app__form_input mb-4'
                    ],
                ]
            )
            ->add(
                'newPasswordConfirm',
                PasswordType::class,
                [
                    'toggle' => true,
                    'hidden_label' => 'Masquer',
                    'visible_label' => 'Afficher',
                    'label' => 'Confirmation du nouveau mot de passe',
                    'attr' => [
                        'class' => 'app__form_input mb-4'
                    ],
                ]
            );
    }


}
