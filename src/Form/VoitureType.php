<?php

namespace App\Form;

use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Positive;
class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('marque', null, [
            'label' => 'Marque',
            'constraints' => [
                new NotBlank([
                    'message' => 'La marque est obligatoire.',
                ]),
                new Length([
                    'max' => 50,
                    'maxMessage' => 'La marque ne peut pas dépasser {{ limit }} caractères.',
                ]),
            ],
        ])
            ->add('modele', null, [
                'label' => 'Modèle',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le modèle est obligatoire.',
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le modèle ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('annee', null, [
                'label' => 'Année',
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'année est obligatoire.',
                    ]),
                    new Range([
                        'min' => 1900,
                        'max' => date('Y'),
                        'notInRangeMessage' => 'L\'année doit être comprise entre {{ min }} et {{ max }}.',
                    ]),
                ],
            ])
            ->add('prix', null, [
                'label' => 'Prix',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prix est obligatoire.',
                    ]),
                    new Positive([
                        'message' => 'Le prix doit être un nombre positif.',
                    ]),
                ],
            ])
            ->add('puissance_fiscale')
            ->add('kilometrage')
            ->add('boite_vitesse')
            ->add('carburant')
            ->add('cylindree')
            ->add('nombre_portes')
            ->add('image', FileType::class, [
                'label' => 'Image de la voiture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
