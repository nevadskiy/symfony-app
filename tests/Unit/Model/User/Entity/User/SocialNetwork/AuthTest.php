<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SocialNetwork;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    /** @test */
    public function it_creates_user_by_social_network(): void
    {
        $name = new Name('John', 'Doe');

        $user = User::signUpBySocialNetwork(
            Id::next(),
            new DateTimeImmutable(),
            $name,
            $socialNetwork = 'vk',
            $identity = '0000001'
        );

        $socialNetworks = $user->getSocialNetworks();

        self::assertTrue($user->isActive());
        self::assertCount(1, $socialNetworks);
        self::assertEquals($socialNetwork, $socialNetworks[0]->getName());
        self::assertEquals($identity, $socialNetworks[0]->getIdentity());
        self::assertEquals($name, $user->getName());
    }
}
