<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Article;
use App\Entity\Author;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'slug',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add('body', TextareaType::class,
                ['constraints' => [new NotBlank()],
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add('imgUploaded', FileType::class,
                ['label' => 'Image(jpg, jpeg, png allowed)',
                    'attr' => ['class' => 'form-control'],
                    'data_class' => null,
                ]
            )
            ->add('author', EntityType::class, [
                    'class' => Author::class,
                    'choice_label' => function (Author $author) {
                        return $author->getFirstname() . ' ' . $author->getLastName();
                    },
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add('create', SubmitType::class,
                ['label' => 'Create',
                    'attr' => ['class' => 'form-control btn-primary pull-right'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
