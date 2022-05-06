<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class NewPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('content', TextareaType::class, [
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new Length([
                    'maxMessage' => "La taille de votre message ne peut excéder {{ limit }} caractères",
                    'max' => 300,
                ]),
            ],
        ])
        ->add('image1', FileType::class, [
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new Image([
                    'maxSize' => '10M',
                    'maxSizeMessage' => "L'image ne peut excéder 10Mo",
                ]),
            ],
        ])
        ->add('image2', FileType::class, [
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new Image([
                    'maxSize' => '10M',
                    'maxSizeMessage' => "L'image ne peut excéder 10Mo",
                ]),
            ],
        ])
        ->add('image3', FileType::class, [
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new Image([
                    'maxSize' => '10M',
                    'maxSizeMessage' => "L'image ne peut excéder 10Mo",
                ]),
            ],
        ])
        ->add('image4', FileType::class, [
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new Image([
                    'maxSize' => '10M',
                    'maxSizeMessage' => "L'image ne peut excéder 10Mo",
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
