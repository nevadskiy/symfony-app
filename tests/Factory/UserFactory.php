<?php

namespace App\Tests\Factory;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;

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
        $user = new User(Id::next(), new DateTimeImmutable());

        if ($this->email) {
            $user->signUpByEmail($this->email, $this->password, $this->token);

            if ($this->confirmed) {
                $user->confirmSignUp();
            }
        }

        if ($this->socialNetwork) {
            $user->signUpBySocialNetwork($this->socialNetwork, $this->identity);
        }

        return $user;
    }
}
