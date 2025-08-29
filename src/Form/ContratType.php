<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Client;
use App\Entity\Contrat;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isUserEdit = $options['is_user_edit']; 

        $builder
            ->add('reference', TextType::class, [
                'disabled' => $isUserEdit,
            ])
            ->add('intitule')
            ->add('date_debut', null, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'disabled' => $isUserEdit,
            ])
            ->add('date_fin', null, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'disabled' => $isUserEdit,
            ])
            ->add('statut', ChoiceType::class, [
                'choices'  => [
                    'Actif' => 'actif',
                    'En_renegociation' => 'en_renegociation',
                    'Expiré' => 'expiré',
                    'Résilié' => 'résilié',
                    'Brouillon' => 'brouillon',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('montant')
            ->add('frequence_paiement', ChoiceType::class, [
                'choices'  => [
                    'Mensuel' => 'mensuel',
                    'Trimestriel' => 'trimestriel',
                    'Annuel' => 'annuel',
                    'Ponctuel' => 'ponctuel',
                ],
                'placeholder' => 'Sélectionnez une fréquence',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('mode_paiement', ChoiceType::class, [
                'choices'  => [
                    'Virement' => 'Virement',
                    'Chèque' => 'Chèque',
                    'Espèces' => 'Espèces',
                ],
                'placeholder' => 'Sélectionnez un mode de paiement',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('description')
            ->add('fichier_contrat', FileType::class, [
                'label' => 'Fichier du contrat',
                'required' => false,
                'mapped' => false, // si ce champ n'est pas lié directement à l'entité
                'attr' => ['class' => 'form-control'],
            ])
            ->add('clauses_particulieres')
            // ->add('clauses', CollectionType::class, [
            //     'entry_type' => ClauseContratType::class,
            //     'entry_options' => ['label' => false],
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'by_reference' => false,
            //     'prototype' => true,
            //     'label' => false,
            // ])
            ->add('date_signature', null, [
                'widget' => 'single_text',
            ])
            ->add('renouvellement_automatique', CheckboxType::class, [
                'required' => false,
                'label' => 'Renouvellement automatique',
            ])
            ->add('date_prochaine_revision', null, [
                'widget' => 'single_text',
            ])
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('updated_at', null, [
                'widget' => 'single_text',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'id',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un utilisateur',
                'required' => true,
                'label' => 'Utilisateur'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
            'is_user_edit' => false, //default is admin/full access
        ]);
    }
}
