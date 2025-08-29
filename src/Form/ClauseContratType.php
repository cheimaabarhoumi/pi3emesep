<?php

namespace App\Form;

use App\Entity\ClauseContrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClauseContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Titre de la clause'
            ])
            ->add('contenu', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 5],
                'label' => 'Contenu de la clause'
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Durée' => 'durée',
                    'Confidentialité' => 'confidentialité',
                    'Paiement' => 'paiement',
                    'Résiliation' => 'résiliation',
                    'Responsabilités' => 'responsabilités',
                    'Renouvellement' => 'renouvellement',
                    'Pénalités' => 'pénalités',
                    'Autre' => 'autre'
                ],
                'placeholder' => 'Sélectionnez un type de clause',
                'attr' => ['class' => 'form-select'],
                'label' => 'Type de clause'
            ])
            ->add('ordre', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Ordre d\'affichage',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClauseContrat::class,
        ]);
    }
} 