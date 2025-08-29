<?php

namespace App\Form;

use App\Entity\SuiviSponsoring;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuiviSponsoringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateSuivi', DateType::class, [
                'label' => 'Date du suivi',
                'widget' => 'single_text'
            ])
            ->add('rapport', TextareaType::class, [
                'label' => 'Rapport',
                'required' => false
            ])
            ->add('etatSuivi', ChoiceType::class, [
                'label' => 'État du suivi',
                'choices' => [
                    'En cours' => 'En_Cours',
                    'Terminé' => 'Terminé',
                    'Rejeté' => 'Rejeté'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SuiviSponsoring::class,
        ]);
    }
}