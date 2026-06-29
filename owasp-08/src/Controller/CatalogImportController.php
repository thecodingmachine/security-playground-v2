<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CatalogImport;
use App\Entity\CatalogProduct;
use App\Entity\User;
use App\Repository\CatalogImportRepository;
use App\Repository\CatalogProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class CatalogImportController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge Software or Data Integrity Failure
    #[Route('/admin/catalog/import', name: 'catalog_import', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        Security $security,
        CatalogProductRepository $catalogProductRepository,
        CatalogImportRepository $catalogImportRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $action = $request->request->getString('action');
        $previewRows = [];
        $previewFilename = '';
        $previewChecksum = '';

        if ($request->isMethod('POST') && $action === 'preview') {
            $uploadedFile = $request->files->get('catalog_file');

            if (!$uploadedFile instanceof UploadedFile) {
                throw new BadRequestHttpException('Catalog file is required.');
            }

            $previewRows = $this->readRows($uploadedFile);
            $previewFilename = $uploadedFile->getClientOriginalName() ?: 'catalog.json';
            $previewChecksum = hash_file('sha256', $uploadedFile->getPathname()) ?: '';
        }

        if ($request->isMethod('POST') && $action === 'apply') {
            $payload = $request->request->getString('import_payload');
            $filename = $request->request->getString('import_filename');
            $checksum = $request->request->getString('import_checksum');

            try {
                /** @var list<array<string, mixed>> $rows */
                $rows = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                throw new BadRequestHttpException('Invalid import payload.');
            }

            // ⚠️ VULNÉRABLE — Software or Data Integrity Failure : l'import applique tous les champs critiques sans contrôle strict.
            foreach ($rows as $row) {
                $sku = isset($row['sku']) && is_string($row['sku']) ? $row['sku'] : '';

                if ($sku === '') {
                    continue;
                }

                $product = $catalogProductRepository->findOneBySku($sku);

                if (!$product instanceof CatalogProduct) {
                    $product = (new CatalogProduct())->setSku($sku);
                    $entityManager->persist($product);
                }

                if (isset($row['name']) && is_string($row['name'])) {
                    $product->setName($row['name']);
                }

                if (isset($row['description']) && is_string($row['description'])) {
                    $product->setDescription($row['description']);
                }

                if (isset($row['price_cents']) && is_numeric($row['price_cents'])) {
                    $product->setPriceCents((int) $row['price_cents']);
                }

                if (isset($row['discount_percent']) && is_numeric($row['discount_percent'])) {
                    $product->setDiscountPercent((int) $row['discount_percent']);
                }

                if (isset($row['is_public'])) {
                    $product->setIsPublic((bool) $row['is_public']);
                }

                if (isset($row['is_featured'])) {
                    $product->setIsFeatured((bool) $row['is_featured']);
                }

                if (isset($row['status']) && is_string($row['status'])) {
                    $product->setStatus($row['status']);
                }
            }

            $import = (new CatalogImport())
                ->setImportedBy($currentUser)
                ->setFilename($filename !== '' ? $filename : 'catalog.json')
                ->setChecksum($checksum)
                ->setRowCount(count($rows))
                ->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($import);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Import appliqué: %d lignes mises à jour.', count($rows)));

            return $this->redirectToRoute('catalog_import');
        }

        return $this->render('catalog/import.html.twig', [
            'preview_rows' => $previewRows,
            'preview_filename' => $previewFilename,
            'preview_checksum' => $previewChecksum,
            'imports' => $catalogImportRepository->findBy([], ['id' => 'DESC']),
            'products' => $catalogProductRepository->findBy([], ['sku' => 'ASC']),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function readRows(UploadedFile $uploadedFile): array
    {
        $extension = mb_strtolower($uploadedFile->getClientOriginalExtension());
        $path = $uploadedFile->getPathname();

        if ($extension === 'json') {
            $content = file_get_contents($path);

            if (!is_string($content)) {
                throw new BadRequestHttpException('Cannot read uploaded JSON file.');
            }

            try {
                /** @var mixed $rows */
                $rows = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                throw new BadRequestHttpException('Invalid JSON import.');
            }

            if (!is_array($rows)) {
                throw new BadRequestHttpException('JSON import must be an array.');
            }

            /** @var list<array<string, mixed>> $normalizedRows */
            $normalizedRows = [];
            foreach ($rows as $row) {
                if (is_array($row)) {
                    /** @var array<string, mixed> $row */
                    $normalizedRows[] = $row;
                }
            }

            return $normalizedRows;
        }

        if ($extension === 'csv') {
            $handle = fopen($path, 'rb');

            if ($handle === false) {
                throw new BadRequestHttpException('Cannot open CSV import.');
            }

            $headers = fgetcsv($handle);
            $rows = [];

            if ($headers === false) {
                fclose($handle);

                return $rows;
            }

            while (($line = fgetcsv($handle)) !== false) {
                /** @var array<string, mixed> $row */
                $row = [];

                foreach ($headers as $index => $header) {
                    if (!is_string($header) || $header === '') {
                        continue;
                    }

                    $row[$header] = $line[$index] ?? null;
                }

                $rows[] = $row;
            }

            fclose($handle);

            return $rows;
        }

        throw new BadRequestHttpException('Only JSON and CSV files are supported.');
    }
}
