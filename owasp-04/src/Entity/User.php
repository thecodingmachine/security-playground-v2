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
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 180, unique: true)]
    private string $username = '';

    #[ORM\Column(length: 120)]
    private string $fullName = '';

    #[ORM\Column]
    private string $password = '';

    /** @var list<string> */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, SensitiveNote>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SensitiveNote::class, orphanRemoval: true)]
    private Collection $sensitiveNotes;

    /**
     * @var Collection<int, PasswordResetToken>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PasswordResetToken::class, orphanRemoval: true)]
    private Collection $passwordResetTokens;

    /**
     * @var Collection<int, IntegrationSecret>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: IntegrationSecret::class, orphanRemoval: true)]
    private Collection $integrationSecrets;

    public function __construct()
    {
        $this->sensitiveNotes = new ArrayCollection();
        $this->passwordResetTokens = new ArrayCollection();
        $this->integrationSecrets = new ArrayCollection();
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

    /**
     * @return Collection<int, SensitiveNote>
     */
    public function getSensitiveNotes(): Collection
    {
        return $this->sensitiveNotes;
    }

    /**
     * @return Collection<int, PasswordResetToken>
     */
    public function getPasswordResetTokens(): Collection
    {
        return $this->passwordResetTokens;
    }

    /**
     * @return Collection<int, IntegrationSecret>
     */
    public function getIntegrationSecrets(): Collection
    {
        return $this->integrationSecrets;
    }
}
