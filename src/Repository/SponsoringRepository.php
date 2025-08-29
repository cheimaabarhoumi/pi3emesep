<?php

namespace App\Repository;

use App\Entity\Sponsoring;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SponsoringRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sponsoring::class);
    }

    // Recherche + tri + filtrage pour la pagination
    public function search(?string $term = null, ?string $etat = null)
    {
        $qb = $this->createQueryBuilder('s');

        if ($term) {
            $qb->andWhere('s.nomPartenaire LIKE :term OR s.typeSponsoring LIKE :term')
               ->setParameter('term', '%' . $term . '%');
        }

        if ($etat) {
            $qb->andWhere('s.etat = :etat')
               ->setParameter('etat', $etat);
        }

        return $qb->orderBy('s.dateDebut', 'DESC');
    }

    public function getMonthlyTotal(\DateTime $date): float
    {
        return (float) $this->createQueryBuilder('s')
            ->select('SUM(s.montant)')
            ->where('s.dateDebut <= :endDate')
            ->andWhere('s.dateFin >= :startDate')
            ->setParameter('startDate', $date->format('Y-m-01'))
            ->setParameter('endDate', $date->format('Y-m-t'))
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function countNewSponsors(\DateTime $date): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.dateDebut BETWEEN :start AND :end')
            ->setParameter('start', $date->format('Y-m-01'))
            ->setParameter('end', $date->format('Y-m-t'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countActiveSponsors(): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.etat = :etat')
            ->setParameter('etat', 'Actif')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTypeDistribution(): array
    {
        $results = $this->createQueryBuilder('s')
            ->select('s.typeSponsoring, COUNT(s.id) as count')
            ->groupBy('s.typeSponsoring')
            ->getQuery()
            ->getResult();

        $distribution = [];
        foreach ($results as $result) {
            $distribution[$result['typeSponsoring']] = $result['count'];
        }
        return $distribution;
    }

    public function getTotalAmount(): float
    {
        return (float) $this->createQueryBuilder('s')
            ->select('SUM(s.montant)')
            ->where('s.etat = :etat')
            ->setParameter('etat', 'Actif')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }
}