<?php

namespace App\Entity;

use App\Repository\SignatureRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Contrat;

#[ORM\Entity(repositoryClass: SignatureRepository::class)]
class Signature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $base64;

    #[ORM\Column]
    private \DateTimeImmutable $signedAt;

    #[ORM\Column(length: 45)]
    private string $ip;

    #[ORM\ManyToOne(targetEntity: Contrat::class, inversedBy: 'signatures')]
    #[ORM\JoinColumn(name: "contrat_id", referencedColumnName: "idContrat", nullable: false)]
    private ?Contrat $contrat = null;

    public function __construct()
    {
        $this->signedAt = new \DateTimeImmutable();
        $this->ip = '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBase64(): string
    {
        return $this->base64;
    }

    public function setBase64(string $base64): self
    {
        $this->base64 = $base64;
        return $this;
    }

    public function getSignedAt(): \DateTimeImmutable
    {
        return $this->signedAt;
    }

    public function setSignedAt(\DateTimeImmutable $signedAt): self
    {
        $this->signedAt = $signedAt;
        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;
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
}
