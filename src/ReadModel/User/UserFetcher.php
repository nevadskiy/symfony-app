<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;

class UserFetcher
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function existsByResetPasswordToken($token): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('user_users')
            ->where('reset_password_token = :token')
            ->setParameter(':token', $token)
            ->execute()
            ->fetchColumn(0) > 0;
    }
}
