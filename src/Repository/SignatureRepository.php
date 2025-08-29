<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Signature;

/**
 * @extends ServiceEntityRepository<Signature>
 */
class SignatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Signature::class);
    }

    // Add custom repository methods if needed...
    public function add(Signature $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    
    public function remove(Signature $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findById(int $id): ?Signature
    {
        return $this->find($id);
    }

    public function findAll(): array
    {
        return $this->findBy([]);
    }

    
}