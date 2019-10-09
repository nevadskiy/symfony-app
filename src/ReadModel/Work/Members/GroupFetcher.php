<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Members;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class GroupFetcher
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function assoc(): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select([
                'id',
                'name',
            ])
            ->from('work_members_groups')
            ->orderBy('name')
            ->execute();

        return array_column($statement->fetchAll(FetchMode::ASSOCIATIVE), 'name', 'id');
    }

    public function all(): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select([
                'g.id',
                'g.name',
                '(SELECT COUNT(*) FROM work_members_members AS m WHERE m.group_id = g.id) AS members',
            ])
            ->from('work_members_groups', 'g')
            ->orderBy('name')
            ->execute();

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }
}
