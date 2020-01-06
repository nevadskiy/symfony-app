<?php

declare(strict_types=1);

namespace App\DataFixtures\Work\Projects;

use App\DataFixtures\Work\Members\MemberFixture;
use App\Model\User\Entity\User\Role;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\Id;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProjectFixture extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_FIRST = 'first';
    public const REFERENCE_SECOND = 'second';

    public function load(ObjectManager $manager): void
    {
        /** @var Member $admin */
        $admin = $this->getReference(MemberFixture::REFERENCE_ADMIN);

        /** @var Member $user */
        $user = $this->getReference(MemberFixture::REFERENCE_USER);

        /** @var Role $manage */
        $manage = $this->getReference(RoleFixture::REFERENCE_MANAGER);

        /** @var Role $guest */
        $guest = $this->getReference(RoleFixture::REFERENCE_GUEST);

        $active = $this->createProject('First Project', 1);

        $active->addDepartment($developmentId = DepartmentId::next(), 'Development');
        $active->addDepartment($marketing = DepartmentId::next(), 'Marketing');

        $active->addMember($admin, [$developmentId], [$manage]);
        $active->addMember($user, [$marketing], [$guest]);

        $manager->persist($active);
        $this->setReference(self::REFERENCE_FIRST, $active);

        $active = $this->createProject('Second Project', 2);

        $active->addDepartment($development = DepartmentId::next(), 'Development');
        $active->addMember($admin, [$development], [$guest]);

        $manager->persist($active);
        $this->setReference(self::REFERENCE_SECOND, $active);

        $archived = $this->createArchivedProject('Third Project', 3);
        $manager->persist($archived);

        $manager->flush();
    }

    private function createArchivedProject(string $name, int $sort): Project
    {
        $project = $this->createProject($name, $sort);
        $project->archive();

        return $project;
    }

    private function createProject(string $name, int $sort): Project
    {
        return new Project(
            Id::next(),
            $name,
            $sort
        );
    }

    public function getDependencies(): array
    {
        return [
            MemberFixture::class,
            RoleFixture::class,
        ];
    }
}
