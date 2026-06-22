<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SensitiveNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SensitiveNoteRepository::class)]
class SensitiveNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 140)]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    private string $encodedValue = '';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'sensitiveNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getEncodedValue(): string
    {
        return $this->encodedValue;
    }

    public function setEncodedValue(string $encodedValue): self
    {
        $this->encodedValue = $encodedValue;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
