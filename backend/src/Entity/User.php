<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'email', length: 180, unique: true)]
    private string $email = '';

    #[ORM\Column(name: 'password', length: 255)]
    private string $password = '';

    #[ORM\Column(name: 'roles', type: 'json')]
    private array $roles = ['ROLE_ADMIN'];

    #[ORM\Column(name: 'display_name', length: 120)]
    private string $displayName = '';

    #[ORM\Column(name: 'active')]
    private bool $active = true;

    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = mb_strtolower(trim($email)); return $this; }
    public function getUserIdentifier(): string { return $this->email; }
    public function getRoles(): array { return array_values(array_unique(array_merge($this->roles, ['ROLE_USER']))); }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }
    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function getDisplayName(): string { return $this->displayName; }
    public function setDisplayName(string $displayName): self { $this->displayName = trim($displayName); return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): self { $this->active = $active; return $this; }
    public function eraseCredentials(): void {}
}
