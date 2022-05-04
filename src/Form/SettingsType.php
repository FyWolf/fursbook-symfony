<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Image;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'disabled'],
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'minMessage' => "Le nom d'utilisateur ne peut faire moins de {{ limit }} caractères",
                        'maxMessage' => "Le nom d'utilisateur ne peut excéder {{ limit }} caractères",
                        // max length allowed by Symfony for security reasons
                        'max' => 25,
                    ]),
                ],
            ])
            ->add('bio', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'disabled'],
                'constraints' => [
                    new Length([
                        'maxMessage' => "Le description ne peut excéder {{ limit }} caractères",
                        // max length allowed by Symfony for security reasons
                        'max' => 180,
                    ]),
                ],
            ])
            ->add('profilePicture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'maxSizeMessage' => "L'image ne peut excéder 10Mo",
                    ]),
                ],
            ])
            ->add('profileBanner', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'maxSizeMessage' => "L'image ne peut excéder 10Mo",
                    ])
                ]
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
