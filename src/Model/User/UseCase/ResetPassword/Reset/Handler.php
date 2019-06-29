<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\ResetPassword\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ResetPasswordTokenizer;
use App\Model\User\Service\ResetPasswordTokenSender;
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
     * @var ResetPasswordTokenizer
     */
    private $tokenizer;
    /**
     * @var ResetPasswordTokenSender
     */
    private $sender;

    public function __construct(
        UserRepository $users,
        ResetPasswordTokenizer $tokenizer,
        ResetPasswordTokenSender $sender,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail(new Email($command->email));

        $user->requestPasswordReset(
            $this->tokenizer->generate(),
            new DateTimeImmutable()
        );

        $this->flusher->flush();

        $this->sender->send($user->getEmail(), $user->getResetPasswordToken());
    }
}
