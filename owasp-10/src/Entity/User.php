<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'app_user')]
#[UniqueEntity(fields: ['username'], message: 'Ce nom utilisateur est déjà utilisé.')]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email est déjà utilisée.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 180, unique: true)]
    private string $username = '';

    #[ORM\Column(length: 180, unique: true)]
    private string $email = '';

    #[ORM\Column(length: 120)]
    private string $fullName = '';

    #[ORM\Column]
    private string $password = '';

    /** @var list<string> */
    #[ORM\Column]
    private array $roles = [];

    /** @var Collection<int, SalesOrder> */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: SalesOrder::class, orphanRemoval: true)]
    private Collection $orders;

    /** @var Collection<int, Invoice> */
    #[ORM\OneToMany(mappedBy: 'issuedTo', targetEntity: Invoice::class, orphanRemoval: true)]
    private Collection $invoices;

    /** @var Collection<int, Report> */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Report::class, orphanRemoval: true)]
    private Collection $reports;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->username !== '' ? $this->username : 'anonymous-user';
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    /** @return Collection<int, SalesOrder> */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /** @return Collection<int, Invoice> */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    /** @return Collection<int, Report> */
    public function getReports(): Collection
    {
        return $this->reports;
    }
}
