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

use ServerStatus\Domain\Model\Alert\AlertTimeWindow;

class AlertTimeWindowDataBuilder
{
    /**
     * @var int
     */
    private $minutes;

    public function __construct()
    {
        $this->minutes = 5;
    }

    public function withValue(int $minutes): AlertTimeWindowDataBuilder
    {
        $this->minutes = $minutes;

        return $this;
    }

    public function build(): AlertTimeWindow
    {
        return new AlertTimeWindow($this->minutes);
    }

    public static function anAlertTimeWindow(): AlertTimeWindowDataBuilder
    {
        return new self();
    }
}
