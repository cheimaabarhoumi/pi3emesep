<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('motdepasse', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'help' => 'Laissez vide pour ne pas changer le mot de passe'
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