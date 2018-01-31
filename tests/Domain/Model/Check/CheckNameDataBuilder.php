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

use ServerStatus\Domain\Model\Check\CheckName;

class CheckNameDataBuilder
{
    private $name;
    private $slug;

    public function __construct()
    {
        $this->name = "My custom check";
        $this->slug = "my-custom-check";
    }

    public function withName(string $name): CheckNameDataBuilder
    {
        $this->name = $name;

        return $this;
    }

    public function withSlug(string $slug): CheckNameDataBuilder
    {
        $this->slug = $slug;

        return $this;
    }

    public function build(): CheckName
    {
        return new CheckName($this->name, $this->slug);
    }

    public static function aCheckName(): CheckNameDataBuilder
    {
        return new self();
    }
}
