<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Role;

use Webmozart\Assert\Assert;

class Permission
{
    public const MANAGE_PROJECT_MEMBERS = 'manage_project_members';

    private $name;

    public function __construct(string $name)
    {
        $this->guard($name);
        $this->name = $name;
    }

    public static function names(): array
    {
        return [
            self::MANAGE_PROJECT_MEMBERS,
        ];
    }

    public function isNameEqual(string $name): bool
    {
        return $this->name === $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function guard(string $name): void
    {
        Assert::oneOf($name, self::names());
    }
}
