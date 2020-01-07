<?php

namespace App\Tests\Factory;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use BadMethodCallException;
use DateTimeImmutable;
use RuntimeException;

class UserFactory
{
    private $id;
    private $registerDate;
    private $email;
    private $password;
    private $name;
    private $token;
    private $role;
    private $socialNetwork;
    private $identity;
    private $confirmed = false;

    public function __construct()
    {
        $this->id = Id::next();
        $this->registerDate = new DateTimeImmutable();
        $this->name = new Name('John', 'Doe');
    }

    public function withRole(Role $role): self
    {
        $clone = clone $this;
        $clone->role = $role;

        return $clone;
    }


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
        $user = null;

        if ($this->email) {
            $user = User::signUpByEmail(
                $this->id,
                $this->registerDate,
                $this->name,
                $this->email,
                $this->password,
                $this->token
            );

            if ($this->confirmed) {
                $user->confirmSignUp();
            }
        }

        if ($this->socialNetwork) {
            $user = User::signUpBySocialNetwork(
                $this->id,
                $this->registerDate,
                $this->name,
                $this->socialNetwork,
                $this->identity
            );
        }

        if (! $user) {
            throw new BadMethodCallException('Specify via method.');
        }

        if ($this->role) {
            $user->changeRole($this->role);
        }

        return $user;
    }
}
