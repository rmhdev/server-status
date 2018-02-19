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

class Check
{
    private $id;
    private $name;
    private $url;

    public function __construct(CheckId $id, CheckName $name, CheckUrl $url)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
    }

    public function id(): CheckId
    {
        return $this->id;
    }

    public function name(): CheckName
    {
        return $this->name;
    }

    public function url(): CheckUrl
    {
        return $this->url;
    }
}
