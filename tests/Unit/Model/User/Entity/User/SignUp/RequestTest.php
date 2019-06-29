<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /** @test */
    public function it_creates_successfully(): void
    {
        $user = new User(
            $id = Id::next(),
            $registerDate = new DateTimeImmutable()
        );

        $user->signUpByEmail(
            $email = new Email('example@mail.com'),
            $password = 'secret',
            $token = 'token'
        );

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        self::assertEquals($id, $user->getId());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($password, $user->getPasswordHash());
        self::assertEquals($registerDate, $user->getRegisterDate());
        self::assertEquals($token, $user->getConfirmToken());
    }

    /** @test */
    public function it_throws_an_exception_when_user_already_signed_up(): void
    {
        $user = new User(Id::next(), new DateTimeImmutable());

        $user->signUpByEmail(new Email('example@mail.com'), 'secret', 'token');

        $this->expectException(DomainException::class);

        $user->signUpByEmail(new Email('example@mail.com'), 'secret', 'token');
    }
}
