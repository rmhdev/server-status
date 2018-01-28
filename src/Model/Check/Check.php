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
    private $name;

    public function __construct(CheckId $id, CheckName $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function id(): CheckId
    {
        return $this->id;
    }

    public function name(): CheckName
    {
        return $this->name;
    }
}
