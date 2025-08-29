<?php

namespace App\Repository;

use App\Entity\Contrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contrat>
 */
class ContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrat::class);
    }

    //    /**
    //     * @return Contrat[] Returns an array of Contrat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Contrat
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getTotalAmount(): float
    {
        try {
            return (float) $this->createQueryBuilder('c')
                ->select('SUM(c.montant)')
                ->getQuery()
                ->getSingleScalarResult() ?? 0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    public function getActiveContractsCount(): int
    {
        try {
            return $this->count(['statut' => 'Actif']);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getRecentContracts(int $limit = 5): array
    {
        try {
            return $this->createQueryBuilder('c')
                ->orderBy('c.dateDebut', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function searchContracts(string $term): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.reference LIKE :term OR c.intitule LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('c.idContrat', 'DESC')
            ->getQuery()
            ->getResult();
    }    

    public function findExpiringSoon(): array
    {
        $now = new \DateTimeImmutable();
        $limitDate = $now->modify('+7 days');

        return $this->createQueryBuilder('c')
            ->where('c.dateFin BETWEEN :now AND :limit')
            ->setParameter('now', $now)
            ->setParameter('limit', $limitDate)
            ->getQuery()
            ->getResult();
    }

    public function updateExpiredContracts(): void
    {
        $qb = $this->createQueryBuilder('c')
            ->update()
            ->set('c.statut', ':expiré')
            ->where('c.dateFin < :now')
            ->andWhere('c.statut != :expiré')
            ->setParameter('expiré', 'expiré')
            ->setParameter('now', new \DateTimeImmutable());
    
        $qb->getQuery()->execute();
    }
    

}
