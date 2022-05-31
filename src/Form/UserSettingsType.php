<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use App\Entity\User;

class UserSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email')
        ->add('oldPassword', PasswordType::class, [
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => "Merci d'entrer un mot de passe",
                ]),
                new Length([
                    'min' => 6,
                    'max' => 4096,
                ]),
            ],
        ])
        ->add('newPassword1', PasswordType::class, [
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => "Merci d'entrer un mot de passe",
                ]),
                new Length([
                    'min' => 6,
                    'max' => 4096,
                ]),
            ],
        ])
        ->add('newPassword2', PasswordType::class, [
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => "Merci d'entrer un mot de passe",
                ]),
                new Length([
                    'min' => 6,
                    'max' => 4096,
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
