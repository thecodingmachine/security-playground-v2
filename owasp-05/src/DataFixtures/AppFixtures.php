<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\CustomerComment;
use App\Entity\Product;
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
            ->setEmail('alice [arobase] acme.local')
            ->setFullName('Alice Martin')
            ->setRoles(['ROLE_USER']);
        $alice->setPassword($this->passwordHasher->hashPassword($alice, 'alice123'));

        $bob = (new User())
            ->setUsername('bob')
            ->setEmail('bob [arobase] acme.local')
            ->setFullName('Bob Durand')
            ->setRoles(['ROLE_USER']);
        $bob->setPassword($this->passwordHasher->hashPassword($bob, 'bob123'));

        $admin = (new User())
            ->setUsername('admin')
            ->setEmail('admin [arobase] acme.local')
            ->setFullName('Admin Sales')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

        $manager->persist($alice);
        $manager->persist($bob);
        $manager->persist($admin);

        $products = [
            (new Product())
                ->setOwner($alice)
                ->setName('Smartphone Nova X')
                ->setDescription('Modèle grand public avec garantie deux ans.')
                ->setPriceCents(69900)
                ->setIsPublic(true),
            (new Product())
                ->setOwner($alice)
                ->setName('Station de charge FastDock')
                ->setDescription('Station de charge professionnelle.')
                ->setPriceCents(12900)
                ->setIsPublic(true),
            (new Product())
                ->setOwner($bob)
                ->setName('Roadmap Produit Q4 interne')
                ->setDescription('Document préparatoire réservé aux équipes produit.')
                ->setPriceCents(0)
                ->setIsPublic(false),
            (new Product())
                ->setOwner($admin)
                ->setName('Tarif Partenaire Secret 2027')
                ->setDescription('Ce produit privé ne doit jamais apparaître dans le catalogue public.')
                ->setPriceCents(9900)
                ->setIsPublic(false),
        ];

        foreach ($products as $product) {
            $manager->persist($product);
        }

        $acme = (new Customer())
            ->setName('Nadia Roy')
            ->setCompany('Acme Retail');
        $globex = (new Customer())
            ->setName('Louis Besson')
            ->setCompany('Globex Distribution');

        $manager->persist($acme);
        $manager->persist($globex);

        $manager->persist(
            (new CustomerComment())
                ->setCustomer($acme)
                ->setAuthor($alice)
                ->setContent('Client prioritaire, demande un suivi hebdomadaire.')
                ->setCreatedAt(new \DateTimeImmutable('-8 hours'))
        );

        $manager->persist(
            (new CustomerComment())
                ->setCustomer($globex)
                ->setAuthor($bob)
                ->setContent('Renouvellement du contrat prévu la semaine prochaine.')
                ->setCreatedAt(new \DateTimeImmutable('-4 hours'))
        );

        $manager->flush();
    }
}
