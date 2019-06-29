<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SocialNetwork;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    /** @test */
    public function it_creates_user_by_social_network(): void
    {
        $user = new User(Id::next(), new DateTimeImmutable());

        $user->signUpBySocialNetwork(
            $socialNetwork = 'vk',
            $identity = '0000001'
        );

        $socialNetworks = $user->getSocialNetworks();

        self::assertTrue($user->isActive());
        self::assertCount(1, $socialNetworks);
        self::assertEquals($socialNetwork, $socialNetworks[0]->getName());
        self::assertEquals($identity, $socialNetworks[0]->getIdentity());
    }

    /** @test */
    public function it_throws_an_exception_if_already_confirmed(): void
    {
        $user = new User(Id::next(), new DateTimeImmutable());

        $user->signUpBySocialNetwork('vk', '0000001');

        $this->expectException(DomainException::class);

        $user->signUpBySocialNetwork('vk', '0000001');
    }
}
