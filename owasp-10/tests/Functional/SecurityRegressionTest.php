<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\InvoiceRepository;
use App\Repository\ReportRepository;
use App\Repository\SalesOrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SecurityRegressionTest extends WebTestCase
{
    public function testRoleUserGetsForbiddenOnSecureAdminEndpoint(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $alice = $userRepository->findOneBy(['username' => 'alice']);

        self::assertInstanceOf(User::class, $alice);

        $client->loginUser($alice);
        $client->request('GET', '/admin/accounts');

        self::assertResponseStatusCodeSame(403);
    }

    public function testPermissionServiceFailureRefusesSensitiveExport(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['username' => 'admin']);

        self::assertInstanceOf(User::class, $admin);

        $client->loginUser($admin);
        $client->request('GET', '/finance/export/secure?download=1&simulate_failure=1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testSecureOrderConfirmationRollsBackWhenPaymentValidationFails(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var SalesOrderRepository $salesOrderRepository */
        $salesOrderRepository = $container->get(SalesOrderRepository::class);
        /** @var InvoiceRepository $invoiceRepository */
        $invoiceRepository = $container->get(InvoiceRepository::class);

        $alice = $userRepository->findOneBy(['username' => 'alice']);
        self::assertInstanceOf(User::class, $alice);

        $order = $salesOrderRepository->findOneBy(['reference' => 'ORDER-2026-0001']);
        self::assertNotNull($order);

        $client->loginUser($alice);
        $client->request('POST', sprintf('/orders/%d/confirm/secure', $order->getId()), [
            'simulate_failure' => '1',
        ]);

        self::assertResponseStatusCodeSame(409);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        $entityManager->clear();
        $reloadedOrder = $salesOrderRepository->findOneBy(['reference' => 'ORDER-2026-0001']);
        self::assertNotNull($reloadedOrder);

        self::assertSame('pending', $reloadedOrder->getStatus());
        self::assertNull($invoiceRepository->findOneBySalesOrder($reloadedOrder));
    }

    public function testSecureReportExportDoesNotExposeStackTraceOrServerPath(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var ReportRepository $reportRepository */
        $reportRepository = $container->get(ReportRepository::class);

        $admin = $userRepository->findOneBy(['username' => 'admin']);
        self::assertInstanceOf(User::class, $admin);

        $report = $reportRepository->findOneBy([
            'title' => 'Rapport conformité trimestriel',
        ]);
        self::assertNotNull($report);

        $client->loginUser($admin);
        $client->request('GET', sprintf('/reports/%d/export/secure', $report->getId()));

        self::assertResponseStatusCodeSame(500);
        $content = (string) $client->getResponse()->getContent();

        self::assertStringNotContainsString('trace', $content);
        self::assertStringNotContainsString('/var/www/html', $content);
        self::assertStringNotContainsString('Unable to generate report', $content);
    }
}
