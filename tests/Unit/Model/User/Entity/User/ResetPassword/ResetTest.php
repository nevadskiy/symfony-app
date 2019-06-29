<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\ResetPassword;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\ResetPasswordToken;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /** @test */
    public function it_can_request_reset_password_token(): void
    {
        $now = new DateTimeImmutable();
        $token = new ResetPasswordToken('token', $now->modify('+1 day'));
        $user = $this->makeSignedUpUserByEmail();

        $user->requestPasswordReset($token, $now);

        self::assertEquals($token, $user->getResetPasswordToken());
    }

    /** @test */
    public function it_throws_an_exception_if_already_requested(): void
    {
        $now = new DateTimeImmutable();
        $token = new ResetPasswordToken('token', $now->modify('+1 day'));
        $user = $this->makeSignedUpUserByEmail();

        $user->requestPasswordReset($token, $now);

        $this->expectException(DomainException::class);

        $user->requestPasswordReset($token, $now);
    }

    /** @test */
    public function it_requests_a_new_token_if_current_is_already_expired(): void
    {
        $now = new DateTimeImmutable();
        $user = $this->makeSignedUpUserByEmail();

        $oldToken = new ResetPasswordToken('token', $now->modify('+1 day'));
        $user->requestPasswordReset($oldToken, $now);
        self::assertEquals($oldToken, $user->getResetPasswordToken());

        $newToken = new ResetPasswordToken('token', $now->modify('+3 days'));
        $user->requestPasswordReset($newToken, $now->modify('+2 days'));
        self::assertEquals($newToken, $user->getResetPasswordToken());
    }

    /** @test */
    public function it_throws_an_exception_when_user_is_signed_up_without_email(): void
    {
        $now = new DateTimeImmutable();
        $token = new ResetPasswordToken('token', $now->modify('+1 day'));
        $user = $this->makeUser();

        $this->expectException(DomainException::class);

        $user->requestPasswordReset($token, $now);
    }

    private function makeSignedUpUserByEmail(): User
    {
        $user = $this->makeUser();

        $user->signUpByEmail(new Email('example@mail.com'), 'secret', 'token');

        return $user;
    }

    private function makeUser(): User
    {
        return new User(Id::next(), new DateTimeImmutable());
    }
}
