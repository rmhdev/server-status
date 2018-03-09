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

use ServerStatus\Domain\Model\Check\CheckStatus;

class CheckStatusDataBuilder
{
    private $code;

    public function __construct()
    {
        $this->code = "";
    }

    public function withCode(string $code): CheckStatusDataBuilder
    {
        $this->code = $code;

        return $this;
    }

    public function build(): CheckStatus
    {
        return new CheckStatus($this->code);
    }

    public static function aCheckStatus(): CheckStatusDataBuilder
    {
        return new self();
    }
}
