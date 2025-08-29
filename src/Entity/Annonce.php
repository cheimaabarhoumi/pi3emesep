<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Titre de l'annonce 
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $titre = null;

    // Description de l'annonce 
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // Relation obligatoire avec une voiture
    #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]   
     private ?Voiture $voiture = null;

    // ---------------------s
    // Getters et Setters
    // ---------------------
    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => false])]
    private ?bool $valider = false;
    
    // Getter pour valider
    public function getValider(): ?bool
    {
        return $this->valider;
    }
    
    // Setter pour valider
    public function setValider(?bool $valider): self
    {
        $this->valider = $valider;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): self
    {
        $this->voiture = $voiture;
        return $this;
    }
}