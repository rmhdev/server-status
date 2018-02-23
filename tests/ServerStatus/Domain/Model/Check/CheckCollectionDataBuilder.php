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

use ServerStatus\Domain\Model\Check\CheckCollection;

class CheckCollectionDataBuilder
{
    private $checks;

    public function __construct()
    {
        $this->checks = [];
    }

    public function withChecks($checks = []): CheckCollectionDataBuilder
    {
        $this->checks = $checks;

        return $this;
    }

    public function build(): CheckCollection
    {
        return new CheckCollection($this->checks);
    }

    public static function aCheckCollection(): CheckCollectionDataBuilder
    {
        return new self();
    }
}
