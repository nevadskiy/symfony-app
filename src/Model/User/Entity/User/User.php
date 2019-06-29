<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use DomainException;

class User
{
    private const STATUS_WAIT = 'wait';
    private const STATUS_ACTIVE = 'active';

    /**
     * @var Id
     */
    private $id;
    /**
     * @var Email
     */
    private $email;
    /**
     * @var string
     */
    private $password;
    /**
     * @var DateTimeImmutable
     */
    private $registerDate;
    /**
     * @var string
     */
    private $confirmToken;
    /**
     * @var string
     */
    private $status;

    private function __construct(Id $id, DateTimeImmutable $registerDate)
    {
        $this->id = $id;
        $this->registerDate = $registerDate;
    }

    public static function signUpByEmail(
        Id $id,
        Email $email,
        string $password,
        DateTimeImmutable $registerDate,
        string $confirmToken
    ): self
    {
        $user = new self($id, $registerDate);

        $user->email = $email;
        $user->password = $password;
        $user->confirmToken = $confirmToken;
        $user->status = self::STATUS_WAIT;

        return $user;
    }

    public static function signUpBySocialNetwork(
        Id $id,
        Email $email,
        string $password,
        DateTimeImmutable $registerDate,
        string $confirmToken
    ): self
    {
        $user = new self($id, $registerDate);

        $user->email = $email;
        $user->password = $password;
        $user->confirmToken = $confirmToken;
        $user->status = self::STATUS_WAIT;

        return $user;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->password;
    }

    public function getRegisterDate(): DateTimeImmutable
    {
        return $this->registerDate;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function confirmSignUp(): void
    {
        if ($this->isActive()) {
            throw new DomainException('User is already confirmed');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }
}
