<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

final class SecurityRegressionTest extends KernelTestCase
{
    public function testSafeCatalogSearchTreatsPayloadAsValue(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var ProductRepository $productRepository */
        $productRepository = $container->get(ProductRepository::class);
        $payload = '" OR 1=1 -- ';

        /** @var list<array{id:int,name:string,description:string,price_cents:int,is_public:int}> $rows */
        $rows = $productRepository->findPublicCatalogSafe($payload);
        $names = [];

        foreach ($rows as $row) {
            $names[] = $row['name'];
        }

        self::assertNotContains('Tarif Partenaire Secret 2027', $names);
    }

    public function testSafeInternalSearchBlocksUnionExtraction(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var ProductRepository $productRepository */
        $productRepository = $container->get(ProductRepository::class);
        $payload = '" UNION SELECT id, email, "user" FROM app_user -- ';

        /** @var list<array{id:int,name:string,type:string}> $rows */
        $rows = $productRepository->findInternalSearchSafe($payload);
        $names = [];
        $types = [];

        foreach ($rows as $row) {
            $names[] = $row['name'];
            $types[] = $row['type'];
        }

        self::assertNotContains('alice [arobase] acme.local', $names);
        self::assertNotContains('user', $types);
    }

    public function testXssPayloadIsEscapedInPatchedTemplates(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var Environment $twig */
        $twig = $container->get('twig');
        $payload = '<script>alert("xss-demo")</script>';

        $htmlEscaped = $twig->createTemplate('{{ payload }}')->render([
            'payload' => $payload,
        ]);
        $jsEscaped = $twig->createTemplate('{{ payload|e(\'js\') }}')->render([
            'payload' => $payload,
        ]);

        self::assertStringNotContainsString('<script>', $htmlEscaped);
        self::assertStringContainsString('&lt;script&gt;', $htmlEscaped);
        self::assertStringNotContainsString('<script>', $jsEscaped);
    }
}
