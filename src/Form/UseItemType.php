<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\UseCategory;
use App\Entity\UseItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UseItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => UseCategory::class,
                'choice_label' => 'nameFr',
                'query_builder' => static fn ($repository) => $repository->createQueryBuilder('c')->orderBy('c.position', 'ASC'),
            ])
            ->add('nameFr', TextType::class, ['label' => 'Nom (FR)'])
            ->add('nameEn', TextType::class, ['label' => 'Nom (EN)'])
            ->add('valueFr', TextType::class, ['label' => 'Valeur (FR)'])
            ->add('valueEn', TextType::class, ['label' => 'Valeur (EN)'])
            ->add('position', IntegerType::class, ['label' => "Position (ordre d'affichage)"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UseItem::class,
        ]);
    }
}
