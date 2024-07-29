<?php

declare(strict_types=1);

namespace Infrastructure\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ResetPasswordRequestFormType extends AbstractType
{
    /**
     * @param array<string, mixed> $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'autocomplete' => 'email',
                    'class' => 'splitscreen__input',
                ],
                'label_attr' => [
                    'class' => 'block mb-2 text-sm text-gray-600 dark:text-gray-200',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'adresse email ne peut pas être vide.'
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
