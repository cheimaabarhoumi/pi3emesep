<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: "App\Repository\ContratRepository")]
#[ORM\Table(name: "contrat")]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idContrat", type: "integer")]
    private ?int $idContrat = null;

    #[ORM\Column(type: "string", length: 50, unique: true)]
    #[Assert\NotBlank(message: "La référence est obligatoire.")]
    private string $reference;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "L'intitulé est obligatoire.")]
    private string $intitule;

    #[ORM\Column(type: "date", nullable: true)]
    #[Assert\NotBlank(message: 'La date de début est obligatoire.')]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: "date", nullable: true)]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire.')]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(type: "string", length: 20, options: ["default" => "brouillon"])]
    #[Assert\Choice(["brouillon", "actif", "expiré", "résilié", "en_renegociation"])]
    private string $statut = "brouillon";

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: "contrats")]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id")]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "contrats")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private ?User $user = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le montant est obligatoire.')]
    private string $montant;

    #[ORM\Column(type: "string", length: 20)]
    #[Assert\Choice(["mensuel", "trimestriel", "annuel", "ponctuel"])]
    #[Assert\NotBlank(message: 'La fréquence de paiement est obligatoire.')]
    private string $frequence_paiement;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $mode_paiement = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $fichier_contrat = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $clauses_particulieres = null;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $date_signature = null;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $renouvellement_automatique = false;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $date_prochaine_revision = null;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $updated_at;

    #[ORM\OneToMany(targetEntity: ClauseContrat::class, mappedBy: "contrat", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $clauses;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->clauses = new ArrayCollection();
    }

    // Getters/Setters (ajoutez pour tous les champs)
    public function getIdContrat(): ?int { return $this->idContrat; }
    public function getReference(): string { return $this->reference; }
    public function setReference(string $reference): self { $this->reference = $reference; return $this; }

    public function getIntitule(): string { return $this->intitule; }
    public function setIntitule(string $intitule): self { $this->intitule = $intitule; return $this; }

    public function getDateDebut(): \DateTimeInterface { return $this->date_debut; }
    public function setDateDebut(?\DateTimeInterface $date): self { $this->date_debut = $date; return $this; }

    public function getDateFin(): \DateTimeInterface { return $this->date_fin; }
    public function setDateFin(?\DateTimeInterface $date): self { $this->date_fin = $date; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): self { 
        if ($client !== null && $client !== $this->client) {
            $client->addContrat($this);
        }
        if ($client === null && $this->client !== null) {
            $this->client->removeContrat($this);
        }
        $this->client = $client; 
        return $this; 
    }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { 
        if ($user !== null && $user !== $this->user) {
            $user->addContrat($this);
        }
        if ($user === null && $this->user !== null) {
            $this->user->removeContrat($this);
        }
        $this->user = $user; 
        return $this; 
    }

    public function getMontant(): string { return $this->montant; }
    public function setMontant(string $montant): self { $this->montant = $montant; return $this; }

    public function getFrequencePaiement(): string { return $this->frequence_paiement; }
    public function setFrequencePaiement(string $frequence): self { $this->frequence_paiement = $frequence; return $this; }

    public function getModePaiement(): ?string { return $this->mode_paiement; }
    public function setModePaiement(?string $mode): self { $this->mode_paiement = $mode; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getFichierContrat(): ?string { return $this->fichier_contrat; }
    public function setFichierContrat(?string $fichier): self { $this->fichier_contrat = $fichier; return $this; }

    public function getClausesParticulieres(): ?string { return $this->clauses_particulieres; }
    public function setClausesParticulieres(?string $clauses): self { $this->clauses_particulieres = $clauses; return $this; }

    public function getDateSignature(): ?\DateTimeInterface { return $this->date_signature; }
    public function setDateSignature(?\DateTimeInterface $date): self { $this->date_signature = $date; return $this; }

    public function isRenouvellementAutomatique(): bool { return $this->renouvellement_automatique; }
    public function setRenouvellementAutomatique(bool $renouvellement): self { $this->renouvellement_automatique = $renouvellement; return $this; }

    public function getDateProchaineRevision(): ?\DateTimeInterface { return $this->date_prochaine_revision; }
    public function setDateProchaineRevision(?\DateTimeInterface $date): self { $this->date_prochaine_revision = $date; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->created_at; }
    public function setCreatedAt(\DateTimeInterface $date): self { $this->created_at = $date; return $this; }

    public function getUpdatedAt(): \DateTimeInterface { return $this->updated_at; }
    public function setUpdatedAt(\DateTimeInterface $date): self { $this->updated_at = $date; return $this; }

    public function getClauses(): Collection
    {
        return $this->clauses;
    }

    public function addClause(ClauseContrat $clause): self
    {
        if (!$this->clauses->contains($clause)) {
            $this->clauses->add($clause);
            $clause->setContrat($this);
        }
        return $this;
    }

    public function removeClause(ClauseContrat $clause): self
    {
        if ($this->clauses->removeElement($clause)) {
            if ($clause->getContrat() === $this) {
                $clause->setContrat(null);
            }
        }
        return $this;
    }

    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->date_fin < $this->date_debut) {
            $context->buildViolation('La date de fin doit être postérieure à la date de début.')
                ->atPath('date_fin')
                ->addViolation();
        }
    
        if ($this->date_signature && $this->date_signature < $this->date_debut) {
            $context->buildViolation('La date de signature doit être postérieure ou égale à la date de début.')
                ->atPath('date_signature')
                ->addViolation();
        }
    
        if ($this->date_prochaine_revision) {
            if ($this->date_prochaine_revision < $this->date_debut || $this->date_prochaine_revision < $this->date_fin) {
                $context->buildViolation('La date de prochaine révision doit être postérieure à la date de début et de fin.')
                    ->atPath('date_prochaine_revision')
                    ->addViolation();
            }
        }
    }
    
}