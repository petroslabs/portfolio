<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\HubLink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HubLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('labelFr', TextType::class, ['label' => 'Libellé (FR)'])
            ->add('labelEn', TextType::class, ['label' => 'Libellé (EN)'])
            ->add('url', TextType::class, ['label' => 'URL'])
            ->add('icon', ChoiceType::class, [
                'label' => 'Icône',
                'choices' => [
                    'GitHub' => 'github',
                    'Email' => 'mail',
                    'Blog' => 'blog',
                    'Projets' => 'projects',
                    "L'établi" => 'uses',
                    'LinkedIn' => 'linkedin',
                    'Générique' => 'link',
                ],
            ])
            ->add('accent', ChoiceType::class, [
                'label' => 'Accent',
                'choices' => [
                    'Teal' => 'teal',
                    'Bronze' => 'bronze',
                ],
            ])
            ->add('external', CheckboxType::class, ['label' => 'Lien externe', 'required' => false])
            ->add('descriptionFr', TextType::class, ['label' => 'Description (FR)', 'required' => false])
            ->add('descriptionEn', TextType::class, ['label' => 'Description (EN)', 'required' => false])
            ->add('position', IntegerType::class, ['label' => "Position (ordre d'affichage)"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HubLink::class,
        ]);
    }
}
