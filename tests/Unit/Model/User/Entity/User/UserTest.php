<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SocialNetwork;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function it_creates_successfully(): void
    {
        $user = new User(
            $id = Id::next(),
            $registerDate = new DateTimeImmutable()
        );

        self::assertTrue($user->isNew());
        self::assertEquals($id, $user->getId());
        self::assertEquals($registerDate, $user->getRegisterDate());
    }
}
