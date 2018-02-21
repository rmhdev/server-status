<?php
declare(strict_types=1);

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Infrastructure\Persistence\InMemory\User;

use ServerStatus\Domain\Model\User\UserDoesNotExistException;
use ServerStatus\Domain\Model\User\UserId;
use ServerStatus\Domain\Model\User\UserRepository;
use ServerStatus\ServerStatus\Domain\Model\User\User;

class InMemoryUserRepository implements UserRepository
{
    private $users;

    public function __construct()
    {
        $this->users = [];
    }

    /**
     * @return User[]
     */
    private function users(): array
    {
        return $this->users;
    }

    /**
     * @inheritdoc
     */
    public function ofId(UserId $id): ?User
    {
        if (array_key_exists($id->value(), $this->users)) {
            return $this->users[$id->value()];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function add(User $user): UserRepository
    {
        $this->users[$user->id()->value()] = $user;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(User $user): UserRepository
    {
        $id = $user->id()->value();
        if (!array_key_exists($id, $this->users)) {
            throw new UserDoesNotExistException(sprintf(
                'User with id "%s" does not exist',
                $id
            ));
        }
        unset($this->users[$id]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function nextId(): UserId
    {
        return new UserId();
    }
}
