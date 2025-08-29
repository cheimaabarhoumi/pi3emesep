<?php

namespace App\Entity;

use App\Repository\EntretienRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: EntretienRepository::class)]
class Entretien

{ 
    public const STATUS_PENDING = 'En attente';
    public const STATUS_DONE = 'Terminé';
    public const STATUS_CANCELED = 'Annulé';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_entretien", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "type_entretien", type: "string", length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $typeEntretien = null;

    #[ORM\Column(name: "date_planifiee", type: "date")]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual("today")]
    #[Assert\LessThanOrEqual("+2 years")]
    private ?\DateTimeInterface $datePlanifiee = null;

    #[ORM\Column(name: "cout", type: "float")]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\LessThan(10000)]
    private ?float $cout = null;

    #[ORM\Column(name: "status", type: "string", length: 255)]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: "getStatusChoices")]
    private ?string $status = null;

    #[ORM\OneToOne(inversedBy: 'entretien', targetEntity: RendezVous::class,cascade: ['remove'])]
    #[ORM\JoinColumn(name: "rendez_vous_id", referencedColumnName: "id_rdv", nullable: false)]
    private ?RendezVous $rendezVous = null;

    
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeEntretien(): ?string
    {
        return $this->typeEntretien;
    }

    public function setTypeEntretien(string $typeEntretien): static
    {
        $this->typeEntretien = $typeEntretien;

        return $this;
    }

    public function getDatePlanifiee(): ?\DateTimeInterface
    {
        return $this->datePlanifiee;
    }

    public function setDatePlanifiee(\DateTimeInterface $datePlanifiee): static
    {
        $this->datePlanifiee = $datePlanifiee;

        return $this;
    }

    public function getCout(): ?float
    {
        return $this->cout;
    }

    public function setCout(float $cout): static
    {
        $this->cout = $cout;

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
    public function getRendezVous(): ?RendezVous
{
    return $this->rendezVous;
}
public function setRendezVous(RendezVous $rendezVous): static
{
    $this->rendezVous = $rendezVous;

    return $this;
}
    
    public function __toString(): string
{
    return $this->typeEntretien . ' - ' . $this->datePlanifiee->format('d/m/Y');
}
public static function getStatusChoices(): array
    {
        return [
            self::STATUS_PENDING => self::STATUS_PENDING,
            self::STATUS_DONE => self::STATUS_DONE,
            self::STATUS_CANCELED => self::STATUS_CANCELED
        ];
    }
    public function __construct()
{
    $this->status = self::STATUS_PENDING;
}
}
