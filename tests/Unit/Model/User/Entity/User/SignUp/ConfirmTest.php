<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Tests\Factory\UserFactory;
use DomainException;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    /** @test */
    public function it_confirms_successfully(): void
    {
        $user = (new UserFactory())->byEmail()->create();

        $user->confirmSignUp();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getConfirmToken());
    }

    /** @test */
    public function it_throws_an_exception_if_already_confirmed(): void
    {
        $user = (new UserFactory())->byEmail()->create();

        $user->confirmSignUp();

        $this->expectException(DomainException::class);

        $user->confirmSignUp();
    }
}
