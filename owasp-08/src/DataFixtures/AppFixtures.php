<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\CatalogProduct;
use App\Entity\Order;
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
            ->setEmail('alice.client.local')
            ->setFullName('Alice Martin')
            ->setRoles(['ROLE_USER']);
        $alice->setPassword($this->passwordHasher->hashPassword($alice, 'alice123'));

        $bob = (new User())
            ->setUsername('bob')
            ->setEmail('bob.client.local')
            ->setFullName('Bob Durand')
            ->setRoles(['ROLE_USER']);
        $bob->setPassword($this->passwordHasher->hashPassword($bob, 'bob123'));

        $admin = (new User())
            ->setUsername('admin')
            ->setEmail('admin.ops.local')
            ->setFullName('Admin Operations')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

        $manager->persist($alice);
        $manager->persist($bob);
        $manager->persist($admin);

        $manager->persist(
            (new Order())
                ->setCustomer($alice)
                ->setReference('ORDER-2026-0001')
                ->setStatus('pending')
                ->setAmountCents(4999)
                ->setCurrency('EUR')
        );

        $manager->persist(
            (new Order())
                ->setCustomer($alice)
                ->setReference('ORDER-2026-0002')
                ->setStatus('paid')
                ->setAmountCents(12900)
                ->setCurrency('EUR')
        );

        $manager->persist(
            (new Order())
                ->setCustomer($bob)
                ->setReference('ORDER-2026-0003')
                ->setStatus('pending')
                ->setAmountCents(8900)
                ->setCurrency('EUR')
        );

        $manager->persist(
            (new CatalogProduct())
                ->setSku('PHONE-001')
                ->setName('Business Phone')
                ->setDescription('Téléphone professionnel pour usage interne.')
                ->setPriceCents(79900)
                ->setDiscountPercent(0)
                ->setIsPublic(true)
                ->setIsFeatured(true)
                ->setStatus('validated')
        );

        $manager->persist(
            (new CatalogProduct())
                ->setSku('DOCK-002')
                ->setName('Desk Dock Pro')
                ->setDescription('Station de charge pour environnement bureautique.')
                ->setPriceCents(14900)
                ->setDiscountPercent(5)
                ->setIsPublic(true)
                ->setIsFeatured(false)
                ->setStatus('validated')
        );

        $manager->flush();
    }
}
