<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Group;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

class GroupRepository
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Group::class);
        $this->em = $em;
    }

    public function get(Id $id): Group
    {
        /** @var Group $group */
        $group = $this->repo->find($id->getValue());

        if (! $group) {
            throw new EntityNotFoundException('Group is not found');
        }

        return $group;
    }

    public function add(Group $group): void
    {
        $this->em->persist($group);
    }

    public function remove(Group $group): void
    {
        $this->em->remove($group);
    }
}
