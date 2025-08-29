<?php

namespace App\Repository;

use App\Entity\SuiviSponsoring;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuiviSponsoring>
 *
 * @method SuiviSponsoring|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuiviSponsoring|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuiviSponsoring[]    findAll()
 * @method SuiviSponsoring[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiviSponsoringRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuiviSponsoring::class);
    }

    public function save(SuiviSponsoring $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SuiviSponsoring $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}