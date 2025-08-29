<?php

namespace App\Form;

use App\Entity\Entretien;
use App\Entity\RendezVous;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use DateTimeImmutable;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateRdv', DateTimeType::class, [
            'widget' => 'single_text',
            'input' => 'datetime_immutable',
            'html5' => false,
            'format' => 'yyyy-MM-dd HH:mm',
            'attr' => [
                'class' => 'form-control datetime-input',
                'autocomplete' => 'off',
            ],
        ])
        ->add('typeRendezVous', ChoiceType::class, [
            'label' => 'Type de rendez-vous',
            'choices' => [
                'Entretien régulier' => RendezVous::TYPE_ENTRETIEN,
                'Réparation' => RendezVous::TYPE_REPARATION,
                'Contrôle technique' => RendezVous::TYPE_CONTROLE,
                'Visite pré-achat' => RendezVous::TYPE_VISITE,
                'Autre' => RendezVous::TYPE_OTHER,
            ],
            'attr' => [
                'class' => 'form-select',
            ],
            'placeholder' => 'Choisissez un type',
        ])
        
            
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Décrivez le motif du rendez-vous'
                ]
            ])
            
            ->add('status', HiddenType::class, [
                'empty_data' => RendezVous::STATUS_PENDING,
            ])

            ->add('email', TypeTextType::class, [
                'label' => 'Email',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Facultatif (pour confirmation du rendez-vous)'
                ]
            ])

           ;
            
     
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}