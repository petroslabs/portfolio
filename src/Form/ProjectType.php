<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('summaryFr', TextareaType::class, ['label' => 'Résumé (FR)'])
            ->add('summaryEn', TextareaType::class, ['label' => 'Résumé (EN)'])
            ->add('image', TextType::class, [
                'label' => 'Image',
                'help' => 'Chemin relatif à assets/ (ex. images/projects/mon-projet.webp)',
            ])
            ->add('stack', TextType::class, [
                'label' => 'Stack technique',
                'help' => 'Séparée par des virgules (ex. Symfony, Tailwind CSS v4)',
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En cours' => Project::STATUS_IN_PROGRESS,
                    'Terminé' => Project::STATUS_DONE,
                    'Archivé' => Project::STATUS_ARCHIVED,
                ],
            ])
            ->add('repoUrl', TextType::class, ['label' => 'URL du dépôt', 'required' => false])
            ->add('demoUrl', TextType::class, ['label' => 'URL de démo', 'required' => false])
            ->add('position', IntegerType::class, ['label' => "Position (ordre d'affichage)"])
        ;

        $builder->get('stack')->addModelTransformer(new CallbackTransformer(
            static fn (array $stack): string => implode(', ', $stack),
            static fn (?string $stack): array => array_values(array_filter(array_map('trim', explode(',', $stack ?? '')))),
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
