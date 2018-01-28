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

namespace ServerStatus\Tests\Model\Check;

use ServerStatus\Model\Check\Check;
use ServerStatus\Model\Check\CheckId;

class CheckDataBuilder
{
    private $id;

    public function __construct()
    {
        $this->id = CheckIdDataBuilder::aCheckId()->withValue("loremipsum")->build();
    }

    public function withId(CheckId $id): CheckDataBuilder
    {
        $this->id = $id;

        return $this;
    }

    public function build(): Check
    {
        return new Check($this->id);
    }

    public static function aCheck(): CheckDataBuilder
    {
        return new self();
    }
}
