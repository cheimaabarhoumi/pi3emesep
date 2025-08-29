<?php

namespace App\Form;

use App\Entity\Sponsoring;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SponsoringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomPartenaire', TextType::class, [
                'label' => 'Nom du partenaire'
            ])
            ->add('typeSponsoring', ChoiceType::class, [
                'label' => 'Type de sponsoring',
                'choices' => [
                    'Standard – 10€ : Affiche l\'annonce pendant 3 jours en haut de la liste.' => 'Standard',
                    'Premium – 25€ : Affiche l\'annonce pendant 7 jours avec un badge "Sponsorisé" + mise en avant en couleur.' => 'Premium',
                    'Or – 40€ : Affichage pendant 14 jours + position prioritaire + image en surbrillance.' => 'Or',
                    'Platine – 60€ : Mise en avant pendant 30 jours + visibilité sur la page d’accueil + email promotionnel aux utilisateurs.' => 'Platine',
                    'Boost Express (1 jour) – 5€ : Affiche l’annonce en haut de la page pendant 24h.' => 'Boost Express',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Choisissez un type de sponsoring',
            ])
            ->add('montant', NumberType::class, [
                'label' => 'Montant',
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control',
                ],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text'
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text'
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Actif' => 'Actif',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sponsoring::class,
        ]);
    }
}