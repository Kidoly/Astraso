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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;



class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', null, [
                'label' => 'Nom d\'utilisateur'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 20,
                        'maxMessage' => 'Votre mot de passe doit contenir au plus {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('firstName', null, [
                'label' => 'Prénom'
            ])
            ->add('lastName', null, [
                'label' => 'Nom'
            ])
            ->add('biography', null, [
                'label' => 'Biographie'
            ])
            ->add('email', null, [
                'label' => 'Email'
            ])
            ->add('institution', EntityType::class, [
                'label' => 'Établissement',
                'class' => Institution::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Choisissez un établissement',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->where('i.accepted = :accepted')
                        ->setParameter('accepted', true)
                        ->orderBy('i.name', 'ASC');
                }
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
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
