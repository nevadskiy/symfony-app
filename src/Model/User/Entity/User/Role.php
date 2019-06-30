<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;

class Email
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $this->format($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function validate(string $value): void
    {
        Assert::notEmpty($value);

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
    }

    private function format(string $value): string
    {
        return mb_strtolower($value);
    }
}
