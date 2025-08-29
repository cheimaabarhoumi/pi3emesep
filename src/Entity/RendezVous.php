<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    public const STATUS_CONFIRMED = 'confirmé';
public const STATUS_CANCELED = 'annulé';
public const STATUS_PENDING = 'en attente'; // (si besoin)

public const TYPE_ENTRETIEN = 'entretien';
public const TYPE_REPARATION = 'réparation';
public const TYPE_CONTROLE = 'contrôle technique';
public const TYPE_VISITE = 'visite pré-achat';
public const TYPE_OTHER = 'autre';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_rdv", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "La date du rendez-vous est obligatoire")]
    #[Assert\GreaterThan("now", message: "La date doit être dans le futur")]
    private ?\DateTimeImmutable $dateRdv = null;

    #[ORM\Column(name: "status", type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    private ?string $status = null;

    #[ORM\Column(name: "description", type: "string", length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "La description doit faire au moins {{ limit }} caractères",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $description = null;
    
    #[ORM\OneToOne(mappedBy: 'rendezVous', targetEntity: Entretien::class, cascade: ['persist', 'remove'])]
    private ?Entretien $entretien = null;

    #[ORM\Column(name: "email", type: "string", length: 180, nullable: true)]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
    #[Assert\Length(
    max: 180,
    maxMessage: "L'email ne peut pas dépasser {{ limit }} caractères"
)]
private ?string $email = null;


    #[ORM\Column(name: "type_rendez_vous", type: "string", length: 20)]
    #[Assert\NotBlank(message: "Le type de rendez-vous est obligatoire")]
    #[Assert\Choice(
        choices: [
            self::TYPE_ENTRETIEN,
            self::TYPE_REPARATION,
            self::TYPE_CONTROLE,
            self::TYPE_VISITE,
            self::TYPE_OTHER
        ],
        message: "Choisissez un type de rendez-vous valide"
    )]
    private ?string $typeRendezVous = self::TYPE_ENTRETIEN;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRdv(): ?\DateTimeImmutable
    {
        return $this->dateRdv;
    }

    public function setDateRdv(\DateTimeImmutable $dateRdv): static
    {
        $this->dateRdv = $dateRdv;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getEntretien(): ?Entretien
{
    return $this->entretien;
}

public function getEmail(): ?string
{
    return $this->email;
}

public function setEmail(?string $email): static
{
    $this->email = $email;
    return $this;
}


public function getTypeRendezVous(): ?string
{
    return $this->typeRendezVous;
}

public function setTypeRendezVous(string $typeRendezVous): static
{
    $this->typeRendezVous = $typeRendezVous;
    return $this;
}


public function setEntretien(?Entretien $entretien): static
{
    // Lie automatiquement l'entretien à ce rendez-vous
    if ($entretien && $entretien->getRendezVous() !== $this) {
        $entretien->setRendezVous($this);
    }

    $this->entretien = $entretien;

    return $this;
}

public function hasEntretien(): bool
{
    return $this->entretien !== null;
}

public function isConfirmed(): bool
{
    return $this->status === self::STATUS_CONFIRMED;
}

public function confirm(): self
{
    $this->status = self::STATUS_CONFIRMED;
    return $this;
}

public function cancel(): self
{
    $this->status = self::STATUS_CANCELED;
    return $this;
}
public function __construct()
{
    $this->status = self::STATUS_PENDING;
    $this->typeRendezVous = self::TYPE_ENTRETIEN;
}


}
