<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Project;
use DateTimeImmutable;
use DomainException;

class Task
{
    private $id;
    private $project;
    private $author;
    private $date;
    private $name;
    private $content;
    private $type;
    private $progress;
    private $priority;
    private $planDate;
    private $parent;

    public function __construct(
        Id $id,
        Project $project,
        Member $author,
        DateTimeImmutable $date,
        Type $type,
        int $priority,
        string $name,
        ?string $content
    )
    {
        $this->id = $id;
        $this->project = $project;
        $this->author = $author;
        $this->date = $date;
        $this->name = $name;
        $this->content = $content;
        $this->progress = 0;
        $this->type = $type;
        $this->priority = $priority;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getAuthor(): Member
    {
        return $this->author;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function edit(string $name, ?string $content): void
    {
        $this->name = $name;
        $this->content = $content;
    }

    public function plan(?DateTimeImmutable $date): void
    {
        $this->planDate = $date;
    }

    public function getPlanDate(): ?DateTimeImmutable
    {
        return $this->planDate;
    }

    public function setChildOf(?Task $parent): void
    {
        $this->guardCycle($parent);
        $this->parent = $parent;
    }

    public function getParent(): ?Task
    {
        return $this->parent;
    }

    protected function guardCycle(?Task $parent): void
    {
        if ($parent) {
            $current = $parent;
            do {
                if ($current === $this) {
                    throw new DomainException('Cyclomatic children.');
                }
            } while ($current && $current = $current->getParent());
        }
    }
}
