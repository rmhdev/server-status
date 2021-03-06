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

namespace ServerStatus\Tests\Domain\Model\Check;

use ServerStatus\Domain\Model\Check\CheckId;

class CheckIdDataBuilder
{
    private $value;

    public function __construct()
    {
        $this->value = "";
    }

    public function withValue(string $value): CheckIdDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build(): CheckId
    {
        return new CheckId($this->value);
    }

    public static function aCheckId(): CheckIdDataBuilder
    {
        return new self();
    }
}
