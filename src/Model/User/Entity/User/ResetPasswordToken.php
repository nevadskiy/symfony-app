<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class ResetPasswordToken
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;
    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $expiryDate;

    public function __construct(string $token, DateTimeImmutable $expiryDate)
    {
        Assert::notEmpty($token);
        $this->token = $token;
        $this->expiryDate = $expiryDate;
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expiryDate <= $date;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @internal for postLoad callback
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->token);
    }
}
