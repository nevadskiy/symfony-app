<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project\Department;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id
{
    private $value;

    public function __construct(string $value)
    {
        $this->guard($value);
        $this->value = $value;
    }

    public static function next(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function isEqual(Id $id): bool
    {
        return $this->getValue() === $id->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    protected function guard(string $value): void
    {
        Assert::notEmpty($value);
    }
}
