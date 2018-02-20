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

namespace ServerStatus\Domain\Model\Check;

use ServerStatus\Domain\Model\User\UserId;
use ServerStatus\ServerStatus\Domain\Model\Check\CheckCollection;

interface CheckRepository
{
    /**
     * @param CheckId $id
     * @return null|Check
     */
    public function ofId(CheckId $id): ?Check;

    /**
     * @param Check $check
     * @return CheckRepository
     */
    public function add(Check $check): CheckRepository;

    /**
     * @param Check $check
     * @return CheckRepository
     * @throws CheckDoesNotExistException
     */
    public function remove(Check $check): CheckRepository;

    /**
     * @return CheckId
     */
    public function nextId(): CheckId;

    /**
     * @param UserId $id
     * @return CheckCollection
     */
    public function byUser(UserId $id): CheckCollection;
}
