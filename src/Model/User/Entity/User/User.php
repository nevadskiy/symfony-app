<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
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
    private $passwordHash;
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
    /**
     * @var ArrayCollection
     */
    private $socialNetworks;
    /**
     * @var ResetPasswordToken
     */
    private $resetPasswordToken;

    private function __construct(Id $id, DateTimeImmutable $registerDate)
    {
        $this->id = $id;
        $this->registerDate = $registerDate;
        $this->socialNetworks = new ArrayCollection();
    }

    public static function signUpByEmail(
        Id $id,
        DateTimeImmutable $registerDate,
        Email $email,
        string $passwordHash,
        string $confirmToken
    ): self
    {
        $user = new self($id, $registerDate);

        $user->email = $email;
        $user->passwordHash = $passwordHash;
        $user->confirmToken = $confirmToken;
        $user->status = self::STATUS_WAIT;

        return $user;
    }

    public static function signUpBySocialNetwork(
        Id $id,
        DateTimeImmutable $registerDate,
        string $socialNetwork,
        string $identity
    ): self
    {
        $user = new self($id, $registerDate);

        $user->attachNetwork($socialNetwork, $identity);
        $user->status = self::STATUS_ACTIVE;

        return $user;
    }

    private function attachNetwork(string $network, string $identity): void
    {
        /** @var SocialNetwork $n */
        foreach ($this->socialNetworks as $n) {
            if ($n->hasName($network)) {
                throw new DomainException('Social network is already attached.');
            }
        }

        $this->socialNetworks->add(new SocialNetwork($this, $network, $identity));
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
        return $this->passwordHash;
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

    /**
     * @return SocialNetwork[]
     */
    public function getSocialNetworks(): array
    {
        return $this->socialNetworks->toArray();
    }

    public function requestPasswordReset(ResetPasswordToken $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active');
        }

        if (!$this->email) {
            throw new DomainException('Email is not specified');
        }

        if ($this->resetPasswordToken && !$this->resetPasswordToken->isExpiredTo($date)) {
            throw new DomainException('Reset password is already requested');
        }

        $this->resetPasswordToken = $token;
    }

    public function getResetPasswordToken(): ResetPasswordToken
    {
        return $this->resetPasswordToken;
    }

    public function passwordReset(DateTimeImmutable $date, string $passwordHash): void
    {
        if (!$this->resetPasswordToken) {
            throw new DomainException('Resetting password has not been requested');
        }

        if ($this->resetPasswordToken->isExpiredTo($date)) {
            throw new DomainException('Reset password token is expired');
        }

        $this->passwordHash = $passwordHash;
    }
}
