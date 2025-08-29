<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: "App\Repository\ClauseContratRepository")]
#[ORM\Table(name: "clause_contrat")]
class ClauseContrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idClause", type: "integer")]
    private ?int $idClause = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: 'Le titre de la clause est obligatoire.')]
    private string $titre;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: 'Le contenu de la clause est obligatoire.')]
    private string $contenu;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\Choice(["durée", "confidentialité", "paiement", "résiliation", "responsabilités", "renouvellement", "pénalités", "autre"])]
    private string $type;

    #[ORM\ManyToOne(targetEntity: Contrat::class, inversedBy: "clauses")]
    #[ORM\JoinColumn(name: "contrat_id", referencedColumnName: "idContrat")]
    private ?Contrat $contrat = null;

    #[ORM\Column(type: "integer")]
    #[Assert\PositiveOrZero]
    private int $ordre = 0;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $updated_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    public function getIdClause(): ?int
    {
        return $this->idClause;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): self
    {
        $this->contrat = $contrat;
        return $this;
    }

    public function getOrdre(): int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $date): self
    {
        $this->created_at = $date;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $date): self
    {
        $this->updated_at = $date;
        return $this;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updated_at = new \DateTime();
    }


} 