<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('images', FileType::class, [
                'label' => 'Photos',
                'mapped' => false, // Le champ n'est pas mappé à une propriété de l'entité
                'required' => false, // Le champ n'est pas obligatoire
                'multiple' => true, // Autoriser plusieurs fichiers
                'attr' => [
                    'accept' => 'image/*', // Accepter uniquement les fichiers image
                ],
            ])
            ->add('timing', null, [
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
