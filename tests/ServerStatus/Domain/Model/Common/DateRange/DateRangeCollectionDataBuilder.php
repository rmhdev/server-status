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

namespace ServerStatus\Tests\Domain\Model\Common\DateRange;

use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeCollection;

final class DateRangeCollectionDataBuilder
{
    /**
     * @var DateRange[]
     */
    private $dateRanges;

    public function __construct()
    {
        $this->dateRanges = [];
    }

    public function withDateRanges($dateRanges = []): DateRangeCollectionDataBuilder
    {
        $this->dateRanges = $dateRanges;

        return $this;
    }

    public function build(): DateRangeCollection
    {
        return new DateRangeCollection($this->dateRanges);
    }

    public static function aDateRangeCollection(): DateRangeCollectionDataBuilder
    {
        return new self();
    }
}
