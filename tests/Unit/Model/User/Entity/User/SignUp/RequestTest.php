<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /** @test */
    public function it_creates_successfully(): void
    {
        $user = new User(
            $id = Id::next(),
            $email = new Email('test@mail.com'),
            $password = 'secret',
            $registerDate = new DateTimeImmutable()
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($password, $user->getPasswordHash());
        self::assertEquals($registerDate, $user->getRegisterDate());
    }
}
