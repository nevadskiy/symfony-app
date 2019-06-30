<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_social_networks", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"name", "identity"})
 * })
 */
class SocialNetwork
{
    /**
     * @var string
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $identity;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="socialNetworks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    public function __construct(User $user, string $name, string $identity)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->name = $name;
        $this->identity = $identity;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function hasName(string $name): bool
    {
        return $this->name === $name;
    }
}
