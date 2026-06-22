<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CustomerNote;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomerNote>
 */
class CustomerNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerNote::class);
    }

    /**
     * @return list<CustomerNote>
     */
    public function findLatestByUser(User $user): array
    {
        $result = $this->createQueryBuilder('note')
            ->andWhere('note.user = :user')
            ->setParameter('user', $user)
            ->orderBy('note.createdAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        if (!is_array($result)) {
            return [];
        }

        return array_values(array_filter($result, static fn (mixed $note): bool => $note instanceof CustomerNote));
    }
}
