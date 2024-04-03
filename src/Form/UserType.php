<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Institution;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('roles')
            ->add('password')
            ->add('first_name')
            ->add('last_name')
            ->add('biography')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('email')
            ->add('isVerified')
            ->add('image', EntityType::class, [
                'class' => Image::class,
                'choice_label' => 'id',
            ])
            ->add('institution', EntityType::class, [
                'class' => Institution::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
