<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Tests\Factory\UserFactory;
use DomainException;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    /** @test */
    public function it_blocks_successfully(): void
    {
        $user = (new UserFactory())->byEmail()->create();

        $user->block();

        self::assertFalse($user->isActive());
        self::assertTrue($user->isBlocked());
    }

    /** @test */
    public function it_throws_an_exception_if_blocked_already(): void
    {
        $user = (new UserFactory())->byEmail()->create();

        $user->block();

        $this->expectException(DomainException::class);

        $user->block();
    }
}