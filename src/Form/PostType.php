<?php

namespace App\Form;

use App\Entity\Post;
use PhpMyAdmin\Triggers\Timing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre",
                'attr' => ['placeholder' => "Titre du post"],
                'required' => true,
            ])
            ->add('body', TextType::class, [
                'label' => "Description",
                'attr' => ['placeholder' => "Description du post"],
                'required' => true,
            ])
            ->add('images', FileType::class, [
                'label' => 'Photos',
                'mapped' => false, // Le champ n'est pas mappé à une propriété de l'entité
                'required' => false, // Le champ n'est pas obligatoire
                'multiple' => true, // Autoriser plusieurs fichiers
                'attr' => [
                    'accept' => 'image/*', // Accepter uniquement les fichiers image
                ],
            ])
            ->add('timing', TimeType::class, [
                'label' => "Temps de retard",
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
