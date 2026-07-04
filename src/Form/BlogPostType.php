<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\BlogPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BlogPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('slug', TextType::class, [
                'label' => 'Slug',
                'help' => 'Utilisé dans l\'URL : /blog/{slug}',
            ])
            ->add('titleFr', TextType::class, ['label' => 'Titre (FR)'])
            ->add('titleEn', TextType::class, ['label' => 'Titre (EN)'])
            ->add('summaryFr', TextType::class, ['label' => 'Résumé (FR)'])
            ->add('summaryEn', TextType::class, ['label' => 'Résumé (EN)'])
            ->add('contentFr', TextareaType::class, [
                'label' => 'Contenu (FR)',
                'help' => 'Markdown',
                'attr' => ['rows' => 16],
            ])
            ->add('contentEn', TextareaType::class, [
                'label' => 'Contenu (EN)',
                'help' => 'Markdown',
                'attr' => ['rows' => 16],
            ])
            ->add('cover', TextType::class, [
                'label' => 'Image de couverture',
                'required' => false,
                'help' => 'Chemin relatif à assets/ (optionnel)',
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
        ]);
    }
}
