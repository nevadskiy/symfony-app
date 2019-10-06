<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Name;

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
    public $firstName;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $lastName;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromUser(User $user): self
    {
        $command = new static($user->getId()->getValue());
        $command->firstName = $user->getName()->getFirst();
        $command->lastName = $user->getName()->getLast();

        return $command;
    }
}
