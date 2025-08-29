<?php

namespace App\Entity;

use App\Repository\SponsoringRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SponsoringRepository::class)]
#[ORM\Table(name: 'sponsoring')]
#[ORM\HasLifecycleCallbacks]
class Sponsoring
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'nomPartenaire', length: 255)]
    private ?string $nomPartenaire = null;

    #[ORM\Column(type: 'float')]
    private ?float $montant = null;

    #[ORM\Column(name: 'dateDebut', type: 'date')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'dateFin', type: 'date')]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: 'typeSponsoring', length: 255)]
    private ?string $typeSponsoring = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPartenaire(): ?string
    {
        return $this->nomPartenaire;
    }

    public function setNomPartenaire(string $nomPartenaire): self
    {
        $this->nomPartenaire = $nomPartenaire;
        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getTypeSponsoring(): ?string
    {
        return $this->typeSponsoring;
    }

    public function setTypeSponsoring(string $typeSponsoring): self
    {
        $this->typeSponsoring = $typeSponsoring;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        if (!in_array($etat, ['Actif', 'Terminé', 'Annulé'])) {
            throw new \InvalidArgumentException("État invalide");
        }
        $this->etat = $etat;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }
}