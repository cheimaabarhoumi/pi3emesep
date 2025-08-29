<?php

namespace App\Repository;

use App\Entity\ClauseContrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClauseContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClauseContrat::class);
    }

    /**
     * Find clauses by contract ID ordered by their order field
     */
    public function findByContratOrdered(int $contratId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.contrat = :contratId')
            ->setParameter('contratId', $contratId)
            ->orderBy('c.ordre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find clauses by type
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->orderBy('c.ordre', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 