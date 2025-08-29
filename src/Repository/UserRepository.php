<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setMotdepasse($newHashedPassword);
        $this->save($user, true);
    }

    public function searchUsers(string $query = '', string $role = '', string $sortBy = 'id', string $order = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('u');

        if ($query) {
            $qb->andWhere('LOWER(u.nom) LIKE LOWER(:query) OR LOWER(u.prenom) LIKE LOWER(:query) OR LOWER(u.email) LIKE LOWER(:query) OR LOWER(u.cin) LIKE LOWER(:query)')
               ->setParameter('query', '%' . $query . '%');
        }

        if ($role) {
            $qb->andWhere('u.role = :role')
               ->setParameter('role', $role);
        }

        $allowedSortFields = ['id', 'nom', 'prenom', 'email', 'cin', 'role'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'id';
        }

        $order = strtoupper($order);
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'ASC';
        }

        $qb->orderBy('u.' . $sortBy, $order);

        return $qb->getQuery()->getResult();
    }
}
