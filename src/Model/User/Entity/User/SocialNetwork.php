<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Ramsey\Uuid\Uuid;

class SocialNetwork
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $identity;

    public function __construct(User $user, string $name, string $identity)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->name = $name;
        $this->identity = $identity;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function hasName(string $name): bool
    {
        return $this->name === $name;
    }
}
