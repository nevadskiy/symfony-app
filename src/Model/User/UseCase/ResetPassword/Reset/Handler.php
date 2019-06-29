<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\ResetPassword\Reset;

use App\Model\Flusher;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;
use DateTimeImmutable;

class Handler
{
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var Flusher
     */
    private $flusher;
    /**
     * @var PasswordHasher
     */
    private $hasher;

    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->hasher = $hasher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->findByResetPasswordToken($command->token);

        if (!$user) {
            throw new \DomainException('Invalid token');
        }

        $user->passwordReset(
            new DateTimeImmutable(),
            $this->hasher->hash($command->password),
        );

        $this->flusher->flush();
    }
}
