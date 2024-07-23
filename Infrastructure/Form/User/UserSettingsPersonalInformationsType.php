<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Infrastructure\Form\User;


use Infrastructure\Entities\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSettingsPersonalInformationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'firstName',
            null,
            [
                'label' => 'Prénom',
                'attr' => [
                     'class' => 'app__form_input mb-4'
                ],
            ]
        )
            ->add(
                'lastName',
                null,
                [
                    'label' => 'Nom',
                    'attr' => [
                        'class' => 'app__form_input mb-4'
                    ],
                ]
            );
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        return [
            'data_class' => User::class,
        ];
    }

}
