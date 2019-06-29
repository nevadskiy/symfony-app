<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    /** @test */
    public function it_confirms_successfully(): void
    {
        $user = $this->makeSignedUpUser();

        $user->confirmSignUp();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getConfirmToken());
    }

    /** @test */
    public function it_throws_an_exception_if_already_confirmed(): void
    {
        $user = $this->makeSignedUpUser();

        $user->confirmSignUp();

        $this->expectException(DomainException::class);

        $user->confirmSignUp();
    }

    private function makeSignedUpUser(): User
    {
        return User::signUpByEmail(
            Id::next(),
            new Email('test@mail.com'),
            'secret',
            new DateTimeImmutable(),
            'token'
        );
    }
}
