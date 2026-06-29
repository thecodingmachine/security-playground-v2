<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\IntegrationSecret;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntegrationSecret>
 */
class IntegrationSecretRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntegrationSecret::class);
    }

    /**
     * @return list<IntegrationSecret>
     */
    public function findByUser(User $user): array
    {
        $result = $this->createQueryBuilder('secret')
            ->andWhere('secret.user = :user')
            ->setParameter('user', $user)
            ->orderBy('secret.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        if (!is_array($result)) {
            return [];
        }

        return array_values(array_filter($result, static fn (mixed $secret): bool => $secret instanceof IntegrationSecret));
    }
}
