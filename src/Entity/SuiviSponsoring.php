<?php

namespace App\Entity;

use App\Repository\SuiviSponsoringRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuiviSponsoringRepository::class)]
#[ORM\Table(name: 'suivisponsoring')]
#[ORM\HasLifecycleCallbacks]
class SuiviSponsoring
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Sponsoring::class)]
    #[ORM\JoinColumn(name: 'idSponsoring', referencedColumnName: 'id', nullable: false)]
    private ?Sponsoring $sponsoring = null;

    #[ORM\Column(name: 'dateSuivi', type: 'date')]
    private ?\DateTimeInterface $dateSuivi = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $rapport = null;

    #[ORM\Column(name: 'etatSuivi', type: 'string')]
    private ?string $etatSuivi = null;

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

    public function getSponsoring(): ?Sponsoring
    {
        return $this->sponsoring;
    }

    public function setSponsoring(?Sponsoring $sponsoring): self
    {
        $this->sponsoring = $sponsoring;
        return $this;
    }

    public function getDateSuivi(): ?\DateTimeInterface
    {
        return $this->dateSuivi;
    }

    public function setDateSuivi(\DateTimeInterface $dateSuivi): self
    {
        $this->dateSuivi = $dateSuivi;
        return $this;
    }

    public function getRapport(): ?string
    {
        return $this->rapport;
    }

    public function setRapport(?string $rapport): self
    {
        $this->rapport = $rapport;
        return $this;
    }

    public function getEtatSuivi(): ?string
    {
        return $this->etatSuivi;
    }

    public function setEtatSuivi(string $etatSuivi): self
    {
        if (!in_array($etatSuivi, ['En_Cours', 'Terminé', 'Rejeté'])) {
            throw new \InvalidArgumentException("État de suivi invalide");
        }
        $this->etatSuivi = $etatSuivi;
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