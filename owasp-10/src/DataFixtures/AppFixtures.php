<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Report;
use App\Entity\SalesOrder;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $alice = (new User())
            ->setUsername('alice')
            ->setEmail('alice [arobase] operations.local')
            ->setFullName('Alice Martin')
            ->setRoles(['ROLE_USER']);
        $alice->setPassword($this->passwordHasher->hashPassword($alice, 'alice123'));

        $bob = (new User())
            ->setUsername('bob')
            ->setEmail('bob [arobase] operations.local')
            ->setFullName('Bob Durand')
            ->setRoles(['ROLE_USER']);
        $bob->setPassword($this->passwordHasher->hashPassword($bob, 'bob123'));

        $admin = (new User())
            ->setUsername('admin')
            ->setEmail('admin [arobase] operations.local')
            ->setFullName('Admin Platform')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

        $manager->persist($alice);
        $manager->persist($bob);
        $manager->persist($admin);

        $order = (new SalesOrder())
            ->setReference('ORDER-2026-0001')
            ->setStatus('pending')
            ->setAmountCents(4999)
            ->setCurrency('EUR')
            ->setOwner($alice)
            ->setCreatedAt(new \DateTimeImmutable('-3 days'));
        $manager->persist($order);

        $manager->persist(
            (new SalesOrder())
                ->setReference('ORDER-2026-0002')
                ->setStatus('pending')
                ->setAmountCents(12900)
                ->setCurrency('EUR')
                ->setOwner($bob)
                ->setCreatedAt(new \DateTimeImmutable('-2 days'))
        );

        $manager->persist(
            (new Report())
                ->setOwner($alice)
                ->setTitle('Performance ventes segment retail')
                ->setStoragePath('/srv/reports/retail-performance-q2.csv')
                ->setIsSensitive(false)
                ->setIsBroken(false)
                ->setCreatedAt(new \DateTimeImmutable('-1 day'))
        );

        $manager->persist(
            (new Report())
                ->setOwner($admin)
                ->setTitle('Export consolidé finance')
                ->setStoragePath('/srv/reports/finance-consolide.csv')
                ->setIsSensitive(true)
                ->setIsBroken(false)
                ->setCreatedAt(new \DateTimeImmutable('-2 hours'))
        );

        $manager->persist(
            (new Report())
                ->setOwner($admin)
                ->setTitle('Rapport conformité trimestriel')
                ->setStoragePath('/srv/reports/missing-compliance-q2.csv')
                ->setIsSensitive(true)
                ->setIsBroken(true)
                ->setCreatedAt(new \DateTimeImmutable('-1 hour'))
        );

        $manager->flush();
    }
}
