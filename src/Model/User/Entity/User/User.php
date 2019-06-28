<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;

class User
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
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

    public function __construct(Id $id, Email $email, string $password, DateTimeImmutable $registerDate)
    {
        $this->email = $email;
        $this->password = $password;
        $this->id = $id;
        $this->registerDate = $registerDate;
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
}
