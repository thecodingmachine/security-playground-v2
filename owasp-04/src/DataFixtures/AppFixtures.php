<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\IntegrationSecret;
use App\Entity\PasswordResetToken;
use App\Entity\SensitiveNote;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $alice = (new User())
            ->setUsername('alice')
            ->setFullName('Alice Martin')
            ->setRoles(['ROLE_USER'])
            ->setPassword(md5('password'));

        $bob = (new User())
            ->setUsername('bob')
            ->setFullName('Bob Durand')
            ->setRoles(['ROLE_USER'])
            ->setPassword(md5('azerty'));

        $admin = (new User())
            ->setUsername('admin')
            ->setFullName('Admin Ops')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword(md5('admin123'));

        $manager->persist($alice);
        $manager->persist($bob);
        $manager->persist($admin);

        $manager->persist(
            (new SensitiveNote())
                ->setUser($alice)
                ->setTitle('Référence fiscale client ACME')
                ->setEncodedValue(base64_encode('FR-ACME-445-TRAINING'))
                ->setCreatedAt(new \DateTimeImmutable('-1 day'))
        );

        $manager->persist(
            (new SensitiveNote())
                ->setUser($alice)
                ->setTitle('Token client sandbox')
                ->setEncodedValue(base64_encode('token-factice-owasp-a04'))
                ->setCreatedAt(new \DateTimeImmutable('-5 hours'))
        );

        $manager->persist(
            (new IntegrationSecret())
                ->setUser($alice)
                ->setName('Connecteur ERP Finance')
                ->setEncryptedValue('Cpplly6lY2+3DKV1p7QwUGdKQzVqN3J2RFFXZ2JkNzQ9')
                ->setCreatedAt(new \DateTimeImmutable('-2 hours'))
        );

        $manager->persist(
            (new PasswordResetToken())
                ->setUser($alice)
                ->setToken(md5('alice'.time()))
                ->setExpiresAt(new \DateTimeImmutable('+7 days'))
                ->setCreatedAt(new \DateTimeImmutable('-30 minutes'))
        );

        $manager->flush();
    }
}
