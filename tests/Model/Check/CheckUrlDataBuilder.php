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

use ServerStatus\Model\Check\CheckUrl;

class CheckUrlDataBuilder
{
    private $method;

    public function __construct()
    {
        $this->method = "get";
    }

    public function withMethod(string $method): CheckUrlDataBuilder
    {
        $this->method = $method;

        return $this;
    }

    public function build(): CheckUrl
    {
        return new CheckUrl($this->method);
    }

    public static function aCheckUrl(): CheckUrlDataBuilder
    {
        return new CheckUrlDataBuilder();
    }
}
