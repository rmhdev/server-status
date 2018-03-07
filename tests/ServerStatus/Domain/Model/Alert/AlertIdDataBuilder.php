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

namespace ServerStatus\Tests\Domain\Model\Alert;

use ServerStatus\Domain\Model\Alert\AlertId;

class AlertIdDataBuilder
{
    private $value;

    public function __construct()
    {
        $this->value = "";
    }

    public function withValue(string $value): AlertIdDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build(): AlertId
    {
        return new AlertId($this->value);
    }

    public static function anAlertId(): AlertIdDataBuilder
    {
        return new self();
    }
}
