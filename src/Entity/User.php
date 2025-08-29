<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\InvalidSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s\-']+$/",
        message: "Le nom ne peut contenir que des lettres, des espaces, des tirets et des apostrophes"
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s\-']+$/",
        message: "Le prénom ne peut contenir que des lettres, des espaces, des tirets et des apostrophes"
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'email ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $email = null;

    #[ORM\Column(length: 8)]
    #[Assert\NotBlank(message: "Le CIN est obligatoire")]
    #[Assert\Length(
        exactMessage: "Le CIN doit contenir exactement 8 caractères",
        min: 8,
        max: 8
    )]
    #[Assert\Regex(
        pattern: "/^[0-9]+$/",
        message: "Le CIN ne peut contenir que des chiffres"
    )]
    private ?string $cin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire")]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le mot de passe ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/",
        message: "Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial"
    )]
    private ?string $motdepasse = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 0,
        max: 5,
        notInRangeMessage: "La réputation doit être comprise entre {{ min }} et {{ max }}"
    )]
    private ?float $reputation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: "La description des services ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $services = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 0,
        notInRangeMessage: "Le tarif ne peut pas être négatif"
    )]
    private ?float $tarif = null;

    #[Assert\Choice(
        choices: ['client', 'prestataire', 'admin'],
        message: "Le rôle doit être soit 'client', 'prestataire' ou 'admin'"
    )]
    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\OneToMany(targetEntity: Contrat::class, mappedBy: "user")]
    private Collection $contrats;

    public function __construct()
    {
        $this->contrats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): static
    {
        $this->cin = $cin;
        return $this;
    }

    public function getMotdepasse(): ?string
    {
        return $this->motdepasse;
    }

    public function setMotdepasse(string $motdepasse): static
    {
        $this->motdepasse = $motdepasse;
        return $this;
    }

    public function getReputation(): ?float
    {
        return $this->reputation;
    }

    public function setReputation(?float $reputation): static
    {
        $this->reputation = $reputation;
        return $this;
    }

    public function getServices(): ?string
    {
        return $this->services;
    }

    public function setServices(?string $services): static
    {
        $this->services = $services;
        return $this;
    }

    public function getTarif(): ?float
    {
        return $this->tarif;
    }

    public function setTarif(?float $tarif): static
    {
        $this->tarif = $tarif;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    // UserInterface methods
    public function getRoles(): array
    {
        $roles = ['ROLE_' . strtoupper($this->role)];
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        
        // If user is admin, add all necessary admin roles
        if ($this->role === 'admin') {
            $roles[] = 'ROLE_ADMIN';
            $roles[] = 'ROLE_CLIENT';
            $roles[] = 'ROLE_PRESTATAIRE';
        }
        
        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->motdepasse;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): self
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setUser($this);
        }
        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            $contrat->setUser(null);
        }
        return $this;
    }
} 