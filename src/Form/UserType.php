<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Institution;
use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => 'Photo',
                'mapped' => false, // Le champ n'est pas mappé à une propriété de l'entité
                'required' => false, // Le champ n'est pas obligatoire
            ])
            ->add('username')
            ->add('password')
            ->add('firstName')
            ->add('lastName')
            ->add('biography')
            ->add('email')
            ->add('institution', EntityType::class, [
                'class' => Institution::class,
                'choice_label' => 'id',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
