<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\ResetPasswordToken;
use DateInterval;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class ResetPasswordTokenizer
{
    private $interval;

    public function __construct(DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generate(): ResetPasswordToken
    {
        return new ResetPasswordToken(
            Uuid::uuid4()->toString(), (new DateTimeImmutable())->add($this->interval)
        );
    }
}
