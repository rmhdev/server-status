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

namespace ServerStatus\Domain\Model\User;

use ServerStatus\ServerStatus\Domain\Model\User\User;

interface UserRepository
{
    /**
     * @param UserId $id
     * @return null|User
     */
    public function ofId(UserId $id): ?User;

    /**
     * @param User $user
     * @return UserRepository
     */
    public function add(User $user): UserRepository;

    /**
     * @param User $user
     * @return UserRepository
     * @throws UserDoesNotExistException
     */
    public function remove(User $user): UserRepository;

    /**
     * @return UserId
     */
    public function nextId(): UserId;
}
