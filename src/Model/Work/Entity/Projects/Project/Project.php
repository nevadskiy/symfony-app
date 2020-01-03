<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use Doctrine\ORM\Mapping as ORM;
use DomainException;
use App\Model\Work\Entity\Projects\Project\Department\Department;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="work_projects_projects")
 */
class Project
{
    /**
     * @var Id
     * @ORM\Column(type="work_projects_project_id")
     * @ORM\Id
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $sort;
    /**
     * @var Status
     * @ORM\Column(type="work_projects_project_status", length=16)
     */
    private $status;
    /**
     * @var ArrayCollection|Department[]
     * @ORM\OneToMany(
     *     targetEntity="App\Model\Work\Entity\Projects\Project\Department\Department",
     *     mappedBy="project", orphanRemoval=true, cascade={"all"}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $departments;

    public function __construct(Id $id, string $name, int $sort)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sort = $sort;
        $this->status = Status::active();
        $this->departments = new ArrayCollection();
    }

    public function edit(string $name, int $sort): void
    {
        $this->name = $name;
        $this->sort = $sort;
    }

    public function archive(): void
    {
        if ($this->isArchived()) {
            throw new DomainException('Project is already archived.');
        }

        $this->status = Status::archived();
    }

    public function reinstate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Project is already active.');
        }

        $this->status = Status::active();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function addDepartment(DepartmentId $id, string $name): void
    {
        foreach ($this->departments as $department) {
            if ($department->isNameEqual($name)) {
                throw new DomainException('Department already exists.');
            }
        }

        $this->departments->add(new Department($this, $id, $name));
    }

    public function editDepartment(DepartmentId $id, string $name): void
    {
        $this->getDepartment($id)->edit($name);
    }

    public function removeDepartment(DepartmentId $id): void
    {
        $this->departments->removeElement(
            $this->getDepartment($id)
        );
    }

    public function getDepartment(DepartmentId $id)
    {
        foreach ($this->departments as $department) {
            if ($department->getId()->isEqual($id)) {
                return $department;
            }
        }

        throw new DomainException('Department is not found.');
    }

    public function getDepartments()
    {
        return $this->departments->toArray();
    }
}
