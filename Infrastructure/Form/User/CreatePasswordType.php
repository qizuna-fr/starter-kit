<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Infrastructure\Form\User;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CreatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                    'label_attr' => [
                        'class' => 'form-control text-sm text-gray-200 dark:text-gray-200'
                    ],
                    'attr' => [
                        'class' => 'splitscreen__input mb-4'
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
                        'class' => 'splitscreen__input mb-4'
                    ],
                    'label_attr' => [
                        'class' => 'form-control text-sm text-gray-200 dark:text-gray-200'
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
