<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Member;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public static function next()
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function validate(string $value): void
    {
        Assert::notEmpty($value);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
