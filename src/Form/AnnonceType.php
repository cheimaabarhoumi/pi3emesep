<?php
namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'annonce'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'choices' => $options['voitures'],
                'choice_label' => function(Voiture $voiture) {
                    return $voiture->getMarque() . ' ' . $voiture->getModele();
                },
                'label' => 'Choisir une voiture'
            ])
            
            ->add('submit', SubmitType::class, [
                'label' => 'Créer l\'annonce'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
            'voitures' => [],
        ]);
    }
}
