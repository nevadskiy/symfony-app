<?php

namespace App\DataFixtures;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const REFERENCE_ADMIN = 'user_user_admin';
    public const REFERENCE_USER = 'user_user_user';

    private $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash('secret');

        $admin = $this->createAdmin(
            new Name('John', 'Kramer'),
            new Email('admin@mail.com'),
            $hash
        );
        $manager->persist($admin);
        $this->setReference(self::REFERENCE_ADMIN, $admin);

        $confirmed = $this->createConfirmed(
            new Name('John', 'Doe'),
            new Email('confirmed@mail.com'),
            $hash
        );
        $manager->persist($confirmed);
        $this->setReference(self::REFERENCE_USER, $confirmed);

        $notConfirmed = $this->createNotConfirmed(
            new Name('John', 'Doe'),
            new Email('not.confirmed@mail.com'),
            $hash
        );
        $manager->persist($notConfirmed);

        $socialNetworked = $this->createRegisteredWithSocialNetwork(
            new Name('John', 'Doe'),
            'facebook',
            '0123456789'
        );
        $manager->persist($socialNetworked);

        $manager->flush();
    }

    private function createAdmin(Name $name, Email $email, string $hash): User
    {
        $user = $this->createConfirmed($name, $email, $hash);

        $user->changeRole(Role::admin());

        return $user;
    }

    private function createConfirmed(Name $name, Email $email, string $hash): User
    {
        $user = $this->createNotConfirmed($name, $email, $hash);

        $user->confirmSignUp();

        return $user;
    }

    private function createNotConfirmed(Name $name, Email $email, string $hash): User
    {
        return User::signUpByEmail(
            Id::next(),
            new DateTimeImmutable(),
            $name,
            $email,
            $hash,
            'token'
        );
    }

    private function createRegisteredWithSocialNetwork(Name $name, string $network, string $identity): User
    {
        return User::signUpBySocialNetwork(
            Id::next(),
            new DateTimeImmutable(),
            $name,
            $network,
            $identity
        );
    }
}
