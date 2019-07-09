<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Tests\Factory\UserFactory;
use DomainException;
use PHPUnit\Framework\TestCase;

class ActivateTest extends TestCase
{
    /** @test */
    public function it_activates_successfully(): void
    {
        $user = (new UserFactory())->byEmail()->create();

        $user->block();

        $user->activate();

        self::assertTrue($user->isActive());
        self::assertFalse($user->isBlocked());
    }

    public function it_throws_an_exception_if_activated_already(): void
    {
        $user = (new UserFactory())->byEmail()->create();

        $user->activate();

        $this->expectException(DomainException::class);

        $user->activate();
    }
}
