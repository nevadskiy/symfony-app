<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use RuntimeException;

class PasswordHasher
{
    public function hash(string $password): string
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        if ($hash === false) {
            throw new RuntimeException('Unable to generate a hash.');
        }

        return $hash;
    }
}
