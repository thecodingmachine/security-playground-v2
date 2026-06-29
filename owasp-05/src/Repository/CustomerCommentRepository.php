<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\CustomerComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomerComment>
 */
class CustomerCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerComment::class);
    }

    /**
     * @return list<CustomerComment>
     */
    public function findByCustomer(Customer $customer): array
    {
        /** @var list<CustomerComment> $comments */
        $comments = $this
            ->createQueryBuilder('comment')
            ->andWhere('comment.customer = :customer')
            ->setParameter('customer', $customer)
            ->orderBy('comment.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $comments;
    }
}
