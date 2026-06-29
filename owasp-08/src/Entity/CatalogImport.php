<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CatalogImportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatalogImportRepository::class)]
class CatalogImport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = 0;

    #[ORM\ManyToOne(inversedBy: 'catalogImports')]
    #[ORM\JoinColumn(nullable: false)]
    private User $importedBy;

    #[ORM\Column(length: 255)]
    private string $filename = '';

    #[ORM\Column(length: 64)]
    private string $checksum = '';

    #[ORM\Column]
    private int $rowCount = 0;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function getId(): ?int
    {
        return $this->id > 0 ? $this->id : null;
    }

    public function getImportedBy(): User
    {
        return $this->importedBy;
    }

    public function setImportedBy(User $importedBy): self
    {
        $this->importedBy = $importedBy;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getChecksum(): string
    {
        return $this->checksum;
    }

    public function setChecksum(string $checksum): self
    {
        $this->checksum = $checksum;

        return $this;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function setRowCount(int $rowCount): self
    {
        $this->rowCount = $rowCount;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
