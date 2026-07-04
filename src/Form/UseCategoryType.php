<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\UseCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UseCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameFr', TextType::class, ['label' => 'Nom (FR)'])
            ->add('nameEn', TextType::class, ['label' => 'Nom (EN)'])
            ->add('position', IntegerType::class, ['label' => "Position (ordre d'affichage)"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UseCategory::class,
        ]);
    }
}
