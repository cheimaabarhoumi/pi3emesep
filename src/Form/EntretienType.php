<?php

namespace App\Form;

use App\Entity\Entretien;
use App\Entity\RendezVous;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EntretienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('typeEntretien', ChoiceType::class, [ // Plutôt que TextType
            'choices' => [
                'Vidange complète' => 'Vidange complète',
                'Contrôle technique' => 'Contrôle technique',
                'Révision' => 'Révision',
                'Autre' => 'Autre'
            ],
            'placeholder' => 'Sélectionnez un type',
            'attr' => ['class' => 'form-select']
        ])
        ->add('datePlanifiee', DateType::class, [
            'label' => 'Date prévue',
            'widget' => 'single_text',
            'html5' => true,
            'attr' => [
                'class' => 'form-control datepicker',
                'min' => (new \DateTime())->format('Y-m-d'),
                'max' => (new \DateTime('+2 years'))->format('Y-m-d')
            ],
            'constraints' => [
                new Assert\NotBlank(['message' => 'Veuillez sélectionner une date']),
                new Assert\GreaterThanOrEqual([
                    'value' => 'today',
                    'message' => 'La date doit être aujourd\'hui ou dans le futur'
                ]),
                new Assert\LessThanOrEqual([
                    'value' => '+2 years',
                    'message' => 'La date ne peut pas dépasser 2 ans dans le futur'
                ])
            ]
        ])
        ->add('cout', TextType::class, [
            'label' => 'Coût estimé (€)',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => '0.00',
                'pattern' => '^\d+(\.\d{1,2})?$',
                'title' => 'Veuillez entrer un nombre valide (ex: 125 ou 125.50)',
                'inputmode' => 'decimal'
            ],
            'constraints' => [
                new Assert\NotBlank(['message' => 'Veuillez saisir un montant']),
                new Assert\Regex([
                    'pattern' => '/^\d+(\.\d{1,2})?$/',
                    'message' => 'Veuillez entrer un nombre valide (ex: 125 ou 125.50)'
                ]),
                new Assert\PositiveOrZero(['message' => 'Le coût ne peut pas être négatif']),
                new Assert\LessThan([
                    'value' => 10000,
                    'message' => 'Le coût ne peut pas dépasser 10 000€'
                ])
            ]
                ]);
       // Seulement ajouter le champ rendezVous si on est en création
    if ($options['is_edit'] === false) {
        $builder->add('rendezVous', EntityType::class, [
            'class' => RendezVous::class,
            'choice_label' => function (RendezVous $rdv) {
                return $rdv->getDateRdv()->format('d/m/Y H:i') . ' - ' . $rdv->getDescription();
                
            },
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('r')
                    ->leftJoin('r.entretien', 'e')
                    ->where('e IS NULL');
            },
            'placeholder' => 'Sélectionnez un rendez-vous',
            'attr' => ['class' => 'form-select'],
        
        ]);
    }
    $builder->add('status', ChoiceType::class, [
            'choices' => array_flip(Entretien::getStatusChoices()),
            'attr' => ['class' => 'form-select']
        ]);

       
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entretien::class,
            'is_edit' => false, // Nouvelle option
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'needs-validation'
                
            ]
            
        ]);

    }
}