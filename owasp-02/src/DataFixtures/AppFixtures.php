<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\CustomerNote;
use App\Entity\Invoice;
use App\Entity\Report;
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
            ->setFullName('Alice Martin')
            ->setRoles(['ROLE_USER']);
        $alice->setPassword($this->passwordHasher->hashPassword($alice, 'alice123'));

        $bob = (new User())
            ->setUsername('bob')
            ->setFullName('Bob Durand')
            ->setRoles(['ROLE_USER']);
        $bob->setPassword($this->passwordHasher->hashPassword($bob, 'bob123'));

        $admin = (new User())
            ->setUsername('admin')
            ->setFullName('Admin Ops')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

        $manager->persist($alice);
        $manager->persist($bob);
        $manager->persist($admin);

        $manager->persist(
            (new Report())
                ->setUser($alice)
                ->setTitle('Rapport de conformité mensuel')
                ->setContent('Synthèse des écarts de configuration détectés.')
                ->setCreatedAt(new \DateTimeImmutable('-1 day'))
        );

        $manager->persist(
            (new Report())
                ->setUser($alice)
                ->setTitle('Inventaire des services exposés')
                ->setContent('Analyse des routes publiques du reverse proxy.')
                ->setCreatedAt(new \DateTimeImmutable('-2 days'))
        );

        $manager->persist(
            (new Invoice())
                ->setUser($alice)
                ->setReference('INV-2026-041')
                ->setAmountCents(18400)
                ->setStatus('paid')
                ->setIssuedAt(new \DateTimeImmutable('-12 days'))
        );

        $manager->persist(
            (new Invoice())
                ->setUser($alice)
                ->setReference('INV-2026-058')
                ->setAmountCents(26550)
                ->setStatus('pending')
                ->setIssuedAt(new \DateTimeImmutable('-3 days'))
        );

        $manager->persist(
            (new CustomerNote())
                ->setUser($alice)
                ->setAccountRef('ACCT-AL-1002')
                ->setNote('Le client demande une validation manuelle des exports sensibles.')
                ->setCreatedAt(new \DateTimeImmutable('-5 hours'))
        );

        $manager->persist(
            (new CustomerNote())
                ->setUser($alice)
                ->setAccountRef('ACCT-AL-1002')
                ->setNote('Prévoir un audit CORS avant la prochaine mise en production.')
                ->setCreatedAt(new \DateTimeImmutable('-2 hours'))
        );

        $manager->flush();
    }
}
