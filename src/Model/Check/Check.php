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

namespace ServerStatus\Model\Check;

class Check
{
    private $id;

    public function __construct(CheckId $id)
    {
        $this->id = $id;
    }

    public function id(): CheckId
    {
        return $this->id;
    }
}
