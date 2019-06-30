<?php

namespace App\Tests\Factory;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use RuntimeException;

class UserFactory
{
    private $email;
    private $password;
    private $token;
    private $socialNetwork;
    private $identity;
    private $confirmed = false;

    public function byEmail(Email $email = null, string $password = null, string $token = null): self
    {
        $this->email = $email ?? new Email('example@mail.com');
        $this->password = $password ?? 'secret-hash';
        $this->token = $token ?? 'token';

        return $this;
    }

    public function confirmed(): self
    {
        $this->confirmed = true;

        return $this;
    }

    public function bySocialNetwork(string $socialNetwork = null, string $identity = null): self
    {
        $this->socialNetwork = $socialNetwork ?? 'vk';
        $this->identity = $identity ?? '000001';

        return $this;
    }

    public function create(): User
    {
        if ($this->email) {
            $user = User::signUpByEmail(
                Id::next(),
                new DateTimeImmutable(),
                $this->email,
                $this->password,
                $this->token
            );

            if ($this->confirmed) {
                $user->confirmSignUp();
            }

            return $user;
        }

        if ($this->socialNetwork) {
            $user = User::signUpBySocialNetwork(
                Id::next(),
                new DateTimeImmutable(),
                $this->socialNetwork,
                $this->identity
            );

            return $user;
        }

        throw new RuntimeException('User factory method is not specified');
    }
}
