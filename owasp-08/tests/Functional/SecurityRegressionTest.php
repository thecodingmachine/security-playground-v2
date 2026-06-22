<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Repository\OrderRepository;
use App\Repository\ProcessedWebhookEventRepository;
use App\Security\IdempotentSettlementService;
use App\Security\SafeCatalogImportService;
use App\Security\SafePaymentReturnService;
use App\Security\SignedWebhookPaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SecurityRegressionTest extends KernelTestCase
{
    public function testWebhookWithoutSignatureCannotMarkOrderAsPaid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        /** @var OrderRepository $orderRepository */
        $orderRepository = $container->get(OrderRepository::class);
        $service = new SignedWebhookPaymentService(
            $orderRepository,
            $entityManager,
            'training-webhook-secret-test'
        );

        $order = $orderRepository->findOneByReference('ORDER-2026-0001');
        self::assertNotNull($order);
        $order->setStatus('pending');
        $entityManager->flush();

        $payload = json_encode([
            'event_id' => 'evt_2026_0001',
            'event_type' => 'payment_succeeded',
            'order_reference' => 'ORDER-2026-0001',
            'amount_cents' => 4999,
            'currency' => 'EUR',
        ], JSON_THROW_ON_ERROR);

        self::assertFalse($service->process($payload, null));

        $entityManager->clear();
        $updatedOrder = $orderRepository->findOneByReference('ORDER-2026-0001');
        self::assertNotNull($updatedOrder);
        self::assertSame('pending', $updatedOrder->getStatus());
    }

    public function testSameEventIdCannotBeProcessedTwice(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var ProcessedWebhookEventRepository $processedWebhookEventRepository */
        $processedWebhookEventRepository = $container->get(ProcessedWebhookEventRepository::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        $service = new IdempotentSettlementService($processedWebhookEventRepository, $entityManager);
        $counter = 0;

        $first = $service->consume('evt_anti_replay_001', static function () use (&$counter): void {
            ++$counter;
        });
        $second = $service->consume('evt_anti_replay_001', static function () use (&$counter): void {
            ++$counter;
        });

        self::assertTrue($first);
        self::assertFalse($second);
        self::assertSame(1, $counter);
    }

    public function testModifiedBrowserReturnCannotMarkOrderAsPaid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        /** @var OrderRepository $orderRepository */
        $orderRepository = $container->get(OrderRepository::class);
        $service = new SafePaymentReturnService($orderRepository);

        $order = $orderRepository->findOneByReference('ORDER-2026-0001');
        self::assertNotNull($order);
        $order->setStatus('pending');
        $entityManager->flush();

        $resolvedOrder = $service->handleReturn('ORDER-2026-0001', 'paid', 1, 'EUR');
        self::assertNotNull($resolvedOrder);

        $entityManager->clear();
        $updatedOrder = $orderRepository->findOneByReference('ORDER-2026-0001');
        self::assertNotNull($updatedOrder);
        self::assertSame('pending', $updatedOrder->getStatus());
    }

    public function testTamperedImportCannotModifyCriticalField(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $service = new SafeCatalogImportService();
        $rows = [[
            'sku' => 'PHONE-001',
            'name' => 'Business Phone',
            'description' => 'Mise a jour locale',
            'price_cents' => 1,
            'discount_percent' => 90,
            'is_public' => true,
            'status' => 'validated',
        ]];

        $sanitized = $service->sanitizeRows($rows);

        self::assertCount(1, $sanitized);
        self::assertArrayNotHasKey('price_cents', $sanitized[0]);
        self::assertArrayNotHasKey('discount_percent', $sanitized[0]);
        self::assertArrayNotHasKey('status', $sanitized[0]);
        self::assertSame('PHONE-001', $sanitized[0]['sku'] ?? null);
    }
}
