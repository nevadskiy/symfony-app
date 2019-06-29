<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Webmozart\Assert\Assert;

class ResetPasswordToken
{
    /**
     * @var string
     */
    private $token;
    /**
     * @var DateTimeImmutable
     */
    private $expireDate;

    public function __construct(string $token, DateTimeImmutable $expireDate)
    {
        Assert::notEmpty($token);
        $this->token = $token;
        $this->expireDate = $expireDate;
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expireDate <= $date;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
