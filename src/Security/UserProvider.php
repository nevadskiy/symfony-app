<?php
declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $users;

    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    public function loadUserByUsername($username): UserInterface
    {
        return $this->makeIdentityUser(
            $this->loadUser($username), $username
        );
    }

    public function refreshUser(UserInterface $identity): UserInterface
    {
        if (!$identity instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . \get_class($identity));
        }

        return $this->makeIdentityUser(
            $this->loadUser($identity->getUsername()), $identity->getUsername()
        );
    }

    public function supportsClass($class): bool
    {
        return $class instanceof UserIdentity;
    }

    private function loadUser(string $username): AuthView
    {
        $user = $this->loadUserFromSocialNetwork($username);

        if ($user) {
            return $user;
        }

        $user = $this->users->findForAuth($username);

        if ($user) {
            return $user;
        }

        throw new UsernameNotFoundException('');
    }

    private function makeIdentityUser(AuthView $user, string $username): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->email ?: $username,
            $user->password_hash ?: '',
            $user->name ?: $username,
            $user->role,
            $user->status
        );
    }

    private function loadUserFromSocialNetwork(string $username): ?AuthView
    {
        $chunks = explode(':', $username);

        if (\count($chunks) !== 2) {
            return null;
        }

        [$socialNetwork, $identity] = $chunks;

        return $this->users->findForAuthBySocialNetwork($socialNetwork, $identity);
    }
}
