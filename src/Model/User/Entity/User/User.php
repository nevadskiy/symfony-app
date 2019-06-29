<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;

class User
{
    private const STATUS_NEW = 'new';
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

    public function __construct(Id $id, DateTimeImmutable $registerDate)
    {
        $this->id = $id;
        $this->registerDate = $registerDate;
        $this->status = self::STATUS_NEW;
        $this->socialNetworks = new ArrayCollection();
    }

    public function signUpByEmail(Email $email, string $passwordHash, string $confirmToken): void
    {
        $this->checkRegistration();

        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->confirmToken = $confirmToken;
        $this->status = self::STATUS_WAIT;
    }

    public function signUpBySocialNetwork(string $socialNetwork, string $identity): void
    {
        $this->checkRegistration();

        $this->attachNetwork($socialNetwork, $identity);
        $this->status = self::STATUS_ACTIVE;
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

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
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
        if ($this->isNew()) {
            throw new DomainException('User creation in the process');
        }

        if ($this->isActive()) {
            throw new DomainException('User is already confirmed');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    private function checkRegistration(): void
    {
        if (!$this->isNew()) {
            throw new DomainException('User is not fully register yet.');
        }
    }

    /**
     * @return SocialNetwork[]
     */
    public function getSocialNetworks(): array
    {
        return $this->socialNetworks->toArray();
    }
}
