<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Model\User\Entity\User\Role;
use App\Tests\Factory\UserFactory;
use DomainException;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    /** @test */
    public function it_creates_successfully(): void
    {
        $user = (new UserFactory())->byEmail()->create();

        $user->changeRole(Role::admin());

        self::assertTrue($user->getRole()->isAdmin());
        self::assertFalse($user->getRole()->isUser());
    }

    /** @test */
    public function it_throws_an_exception_when_trying_to_set_the_same_role(): void
    {
        $user = (new UserFactory())->byEmail()->create();

        $user->changeRole(Role::admin());

        $this->expectException(DomainException::class);

        $user->changeRole(Role::admin());
    }
}
