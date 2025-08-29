<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank(message: "La marque est obligatoire.")]
    #[Assert\Length(max: 50, maxMessage: "La marque ne peut pas dépasser 50 caractères.")]
    private ?string $marque = null;
    
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank(message: "Le modèle est obligatoire.")]
    #[Assert\Length(max: 50, maxMessage: "Le modèle ne peut pas dépasser 50 caractères.")]
    private ?string $modele = null;
    
    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "L'année est obligatoire.")]
    #[Assert\Range(min: 1900, max: 2100, notInRangeMessage: "L'année doit être comprise entre {{ min }} et {{ max }}.")]
    private ?int $annee = null;
    
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private ?string $prix = null;
    
    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "La puissance fiscale est obligatoire.")]
    #[Assert\Positive(message: "La puissance fiscale doit être positive.")]
    private ?int $puissance_fiscale = null;
    
    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "Le kilométrage est obligatoire.")]
    #[Assert\PositiveOrZero(message: "Le kilométrage ne peut pas être négatif.")]
    private ?int $kilometrage = null;
    
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank(message: "La boîte de vitesse est obligatoire.")]
    #[Assert\Length(max: 50, maxMessage: "La boîte de vitesse ne peut pas dépasser 50 caractères.")]
    private ?string $boite_vitesse = null;
    
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank(message: "Le carburant est obligatoire.")]
    #[Assert\Length(max: 50, maxMessage: "Le type de carburant ne peut pas dépasser 50 caractères.")]
    private ?string $carburant = null;
    
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank(message: "La cylindrée est obligatoire.")]
    #[Assert\Length(max: 50, maxMessage: "La cylindrée ne peut pas dépasser 50 caractères.")]
    private ?string $cylindree = null;
    
    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "Le nombre de portes est obligatoire.")]
    #[Assert\Range(min: 2, max: 6, notInRangeMessage: "Le nombre de portes doit être entre {{ min }} et {{ max }}.")]
    private ?int $nombre_portes = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    //#[Assert\NotBlank(message: "L'image est obligatoire.")]
    //#[Assert\Length(max: 255, maxMessage: "Le nom du fichier image ne peut pas dépasser 255 caractères.")]
    private ?string $image = null;
    

    // Getters and Setters

    public function getId(): ?int { return $this->id; }

    public function getMarque(): ?string { return $this->marque; }
    public function setMarque(?string $marque): self { $this->marque = $marque; return $this; }

    public function getModele(): ?string { return $this->modele; }
    public function setModele(?string $modele): self { $this->modele = $modele; return $this; }

    public function getAnnee(): ?int { return $this->annee; }
    public function setAnnee(?int $annee): self { $this->annee = $annee; return $this; }

    public function getPrix(): ?string { return $this->prix; }
    public function setPrix(?string $prix): self { $this->prix = $prix; return $this; }

    public function getPuissanceFiscale(): ?int { return $this->puissance_fiscale; }
    public function setPuissanceFiscale(?int $puissance_fiscale): self { $this->puissance_fiscale = $puissance_fiscale; return $this; }

    public function getKilometrage(): ?int { return $this->kilometrage; }
    public function setKilometrage(?int $kilometrage): self { $this->kilometrage = $kilometrage; return $this; }

    public function getBoiteVitesse(): ?string { return $this->boite_vitesse; }
    public function setBoiteVitesse(?string $boite_vitesse): self { $this->boite_vitesse = $boite_vitesse; return $this; }

    public function getCarburant(): ?string { return $this->carburant; }
    public function setCarburant(?string $carburant): self { $this->carburant = $carburant; return $this; }

    public function getCylindree(): ?string { return $this->cylindree; }
    public function setCylindree(?string $cylindree): self { $this->cylindree = $cylindree; return $this; }

    public function getNombrePortes(): ?int { return $this->nombre_portes; }
    public function setNombrePortes(?int $nombre_portes): self { $this->nombre_portes = $nombre_portes; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }
}