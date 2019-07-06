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

        return $statement->fetch() ?: null;
    }

    public function findForAuthBySocialNetwork(string $socialNetwork, string $identity): ?AuthView
    {
        $statement = $this->connection->createQueryBuilder()
            ->select([
                'u.id',
                'u.email',
                'u.password_hash',
                'u.role',
                'u.status',
            ])
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->where('n.name = :name AND n.identity = :identity')
            ->setParameter(':name', $socialNetwork)
            ->setParameter(':identity', $identity)
            ->execute();

        $statement->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);

        return $statement->fetch() ?: null;
    }

    public function findByEmail(string $email): ?ShortView
    {
        $statement = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        $statement->setFetchMode(FetchMode::CUSTOM_OBJECT, ShortView::class);

        return $statement->fetch() ?: null;
    }
}
