<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class TaskRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $em->getRepository(Task::class);
    }

    public function nextId(): Id
    {
        return new Id((int)$this->connection->query("SELECT nextval('work_projects_tasks_seq')")->fetchColumn());
    }

    public function get(Id $id): Task
    {
        /** @var Task $task */
        $task = $this->repo->find($id->getValue());

        if (! $task) {
            throw new EntityNotFoundException('Task is not found.');
        }

        return $task;
    }

    public function add(Task $task): void
    {
        $this->em->persist($task);
    }

    public function remove(Task $task): void
    {
        $this->em->remove($task);
    }

    /**
     * @param Id $id
     * @return Task[]
     */
    public function allByParent(Id $id): array
    {
        return $this->repo->findBy(['parent' => $id->getValue()]);
    }
}
