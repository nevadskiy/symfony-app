<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Email;

use App\Model\User\Entity\User\Email;
use App\Tests\Factory\UserFactory;
use DomainException;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    /** @test */
    public function it_confirms_email_changing(): void
    {
        $user = (new UserFactory())->byEmail()->confirmed()->create();

        $email = new Email('example-new@mail.com');

        $user->requestEmailChanging($email, 'token');

        self::assertEquals($email, $user->getNewEmail());
        self::assertEquals('token', $user->getNewEmailToken());
    }

    /** @test */
    public function it_throws_an_exception_if_email_is_the_same(): void
    {
        $email = new Email('example@mail.com');

        $user = (new UserFactory())->byEmail($email)->confirmed()->create();

        $this->expectException(DomainException::class);

        $user->requestEmailChanging($email, 'token');
    }

    /** @test */
    public function it_throws_an_exception_if_email_is_not_confirmed(): void
    {
        $email = new Email('example@mail.com');

        $user = (new UserFactory())->byEmail($email)->create();

        $this->expectException(DomainException::class);

        $user->requestEmailChanging(new Email('example-new@mail.com'), 'token');
    }
}
