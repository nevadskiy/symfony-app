<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use LogicException;

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
            ->select('id', 'email', 'role', 'status')
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        $statement->setFetchMode(FetchMode::CUSTOM_OBJECT, ShortView::class);

        return $statement->fetch() ?: null;
    }

    public function findDetails(string $id): ?DetailsView
    {
        $statement = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'register_date',
                'email', 
                'role',
                'status',
                'name_first first_name',
                'name_last last_name'
            )
            ->from('user_users')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $statement->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailsView::class);

        /** @var DetailsView $view */
        $view = $statement->fetch();

        $statement = $this->connection->createQueryBuilder()
            ->select('name', 'identity')
            ->from('user_social_networks')
            ->where('user_id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $statement->setFetchMode(FetchMode::CUSTOM_OBJECT, SocialNetworkView::class);

        $view->socialNetworks = $statement->fetchAll();

        return $view;
    }

    public function getDetails(string $id): DetailsView
    {
        $details = $this->findDetails($id);

        if (!$details) {
            throw new LogicException('User is not found');
        }

        return $details;
    }

    public function findBySignUpConfirmToken(string $token): ?ShortView
    {
        $statement = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('confirm_token = :token')
            ->setParameter(':token', $token)
            ->execute();

        $statement->setFetchMode(FetchMode::CUSTOM_OBJECT, ShortView::class);

        return $statement->fetch() ?: null;
    }
}
