<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

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

    public function findForAuth(string $email): ?AuthView
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('id', 'email', 'password_hash', 'role', 'status')
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        $statement->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);
        $result = $statement->fetch();

        return $result ?: null;
    }
}
