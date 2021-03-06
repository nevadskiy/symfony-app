<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Role;

use Symfony\Component\Validator\Constraints as Assert;
use App\Model\User\Entity\User\User;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $role;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromUser(User $user): self
    {
        $command = new self($user->getId()->getValue());
        $command->role = $user->getRole()->getName();

        return $command;
    }
}
