<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use Webmozart\Assert\Assert;

class Id
{
    private $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    protected function validate(int $value): void
    {
        Assert::notEmpty($value);
    }
}
