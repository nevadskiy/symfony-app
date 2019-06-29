<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\ResetPassword;

use App\Model\User\Entity\User\ResetPasswordToken;
use App\Tests\Factory\UserFactory;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

class ResetTest extends TestCase
{
    /** @test */
    public function it_resets_successfully(): void
    {
        $now = new DateTimeImmutable();
        $token = new ResetPasswordToken('token', $now->modify('+1 day'));
        $user = (new UserFactory())->byEmail()->create();

        $user->requestPasswordReset($token, $now->modify('+1 day'));

        $user->passwordReset($now, 'new-secret-hash');

        self::assertEquals($token, $user->getResetPasswordToken());
        self::assertEquals('new-secret-hash', $user->getPasswordHash());
    }

    /** @test */
    public function it_throws_an_exception_if_token_is_expired(): void
    {
        $now = new DateTimeImmutable();
        $token = new ResetPasswordToken('token', $now);
        $user = (new UserFactory())->byEmail()->create();

        $user->requestPasswordReset($token, $now);

        $this->expectException(DomainException::class);

        $user->passwordReset($now->modify('+1 day'), 'hash');
    }

    /** @test */
    public function it_throws_an_exception_if_token_has_not_been_requests(): void
    {
        $now = new DateTimeImmutable();
        $user = (new UserFactory())->byEmail()->create();

        $this->expectException(DomainException::class);

        $user->passwordReset($now, 'hash');
    }
}
