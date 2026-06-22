<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordResetToken>
 */
class PasswordResetTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    /**
     * @return list<PasswordResetToken>
     */
    public function findLatestByUser(User $user, int $limit = 5): array
    {
        $result = $this->createQueryBuilder('token')
            ->andWhere('token.user = :user')
            ->setParameter('user', $user)
            ->orderBy('token.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        if (!is_array($result)) {
            return [];
        }

        return array_values(array_filter($result, static fn (mixed $token): bool => $token instanceof PasswordResetToken));
    }
}
