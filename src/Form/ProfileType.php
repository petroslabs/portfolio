<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('taglineFr', TextType::class, ['label' => 'Tagline (FR)'])
            ->add('taglineEn', TextType::class, ['label' => 'Tagline (EN)'])
            ->add('bioFr', TextareaType::class, ['label' => 'Bio (FR)'])
            ->add('bioEn', TextareaType::class, ['label' => 'Bio (EN)'])
            ->add('logo', TextType::class, [
                'label' => 'Logo',
                'help' => 'Chemin relatif à assets/ (ex. images/logo.png)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
