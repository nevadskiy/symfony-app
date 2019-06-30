<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SocialNetwork\Auth;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use DateTimeImmutable;
use DomainException;

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

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->hasByNetworkIdentity($command->network, $command->identity);

        if ($user) {
            throw new DomainException('User already exists');
        }

        $user = User::signUpBySocialNetwork(
            Id::next(),
            new DateTimeImmutable(),
            $command->network,
            $command->identity
        );

        $this->users->add($user);

        $this->flusher->flush();
    }
}
