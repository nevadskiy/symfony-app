<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User\Entity\User\User;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, EquatableInterface
{
    private $id;
    private $username;
    private $password;
    private $nameFormatted;
    private $role;
    private $status;

    public function __construct(
        string $id,
        string $username,
        string $password,
        string $nameFormatted,
        string $role,
        string $status
    )
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->nameFormatted = $nameFormatted;
        $this->role = $role;
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNameFormatted()
    {
        return $this->nameFormatted;
    }

    public function isActive(): bool
    {
        return $this->status === User::STATUS_ACTIVE;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return $this->id === $user->id
            && $this->password === $user->password
            && $this->role === $user->role
            && $this->status === $user->status;
    }
}
