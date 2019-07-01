<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var EntityRepository
     */
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function get(Id $id): User
    {
        /** @var User $user */
        $user = $this->repo->find($id->getValue());

        if (!$user) {
            throw new EntityNotFoundException("User is not found by id {$id->getValue()}");
        }

        return $user;
    }

    public function getByEmail(Email $email): User
    {
        /** @var User $user */
        $user = $this->repo->findOneBy(['email' => $email->getValue()]);

        if (!$user) {
            throw new EntityNotFoundException("User is not found by email {$email->getValue()}");
        }

        return $user;
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email->getValue())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    /**
     * @param string $token
     * @return User|null|object
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repo->findOneBy(['confirmToken' => $token]);
    }

    public function hasBySocialNetworkIdentity(string $network, string $identity): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id')
            ->innerJoin('t.social_networks', 'n')
            ->andWhere('n.name = :network and n.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    /**
     * @param string $token
     * @return User|null|object
     */
    public function findByResetPasswordToken(string $token): ?User
    {
        return $this->repo->findOneBy(['resetPasswordToken.token' => $token]);
    }
}
