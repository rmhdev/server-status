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

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Check\CheckUrl;

class CheckDataBuilder
{
    private $id;
    private $name;
    private $url;

    public function __construct()
    {
        $this->id = CheckIdDataBuilder::aCheckId()->withValue("loremipsum")->build();
        $this->name = CheckNameDataBuilder::aCheckName()->withName("My custom check")->build();
        $this->url = CheckUrlDataBuilder::aCheckUrl()->build();
    }

    public function withId(CheckId $id): CheckDataBuilder
    {
        $this->id = $id;

        return $this;
    }

    public function withName(CheckName $name): CheckDataBuilder
    {
        $this->name = $name;

        return $this;
    }

    public function withUrl(CheckUrl $url): CheckDataBuilder
    {
        $this->url = $url;

        return $this;
    }

    public function build(): Check
    {
        return new Check($this->id, $this->name, $this->url);
    }

    public static function aCheck(): CheckDataBuilder
    {
        return new self();
    }
}
