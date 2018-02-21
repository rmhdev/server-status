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

namespace ServerStatus\Application\DataTransformer\User;

use ServerStatus\ServerStatus\Domain\Model\User\User;

final class UserChecksDtoDataTransformer implements UserChecksDataTransformer
{
    /**
     * @var User
     */
    private $user;

    public function write(User $user)
    {
        $this->user = $user;
    }

    public function read()
    {
        return [
            "user" => $this->processUserToArray(),
        ];
    }

    private function processUserToArray(): array
    {
        return [
            "id" => (string) $this->user->id(),
            "alias" => (string) $this->user->alias(),
        ];
    }
}
