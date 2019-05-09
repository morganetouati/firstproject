<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'lastname',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'title',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'firstname',
                TextType::class, [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'email',
                EmailType::class, [
                    'attr' => ['class' => 'form-control'],
                ]
            )

            ->add(
                'company',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'shortBio',
                TextareaType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'phone',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ]
            )
            ->add(
                'facebook',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ]
            )
            ->add(
                'twitter',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ]
            )
            ->add(
                'github',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'attr' => ['class' => 'form-control btn-primary pull-right'],
                    'label' => 'Submit!',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }

    public function getName()
    {
        return 'author_form';
    }
}
