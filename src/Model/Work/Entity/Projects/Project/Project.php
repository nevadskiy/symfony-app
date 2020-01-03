<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Role\Role;
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
    /**
     * @var ArrayCollection|Membership[]
     * @ORM\OneToMany(targetEntity="Membership", mappedBy="project", orphanRemoval=true, cascade={"all"})
     */
    private $memberships;

    public function __construct(Id $id, string $name, int $sort)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sort = $sort;
        $this->status = Status::active();
        $this->departments = new ArrayCollection();
        $this->memberships = new ArrayCollection();
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
        foreach ($this->memberships as $membership) {
            if ($membership->isForDepartment($id)) {
                throw new DomainException('Unable to remove department with members.');
            }
        }

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

    public function addMember(Member $member, array $departmentIds, array $roles): void
    {
        if ($this->getMembership($member->getId())) {
            throw new DomainException('Member already exists.');
        }

        $departments = array_map([$this, 'getDepartment'], $departmentIds);

        $this->memberships->add(new Membership($this, $member, $departments, $roles));
    }

    public function editMember(MemberId $id, array $departmentIds, array $roles): void
    {
        $membership = $this->getMembership($id);

        $membership->changeDepartments(array_map([$this, 'getDepartment'], $departmentIds));
        $membership->changeRoles($roles);
    }

    public function removeMember(MemberId $id): void
    {
        $this->memberships->removeElement(
            $this->getMembership($id)
        );
    }

    protected function getMembership(MemberId $id): Membership
    {
        foreach ($this->memberships as $membership) {
            if ($membership->isForMember($id)) {
                return $membership;
            }
        }

        throw new DomainException('Member is not found.');
    }

    public function getMemberships()
    {
        return $this->memberships->toArray();
    }
}
