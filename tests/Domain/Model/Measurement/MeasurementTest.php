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

namespace ServerStatus\Tests\Domain\Model\Measurement;

use PHPUnit\Framework\TestCase;

class MeasurementTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveImmutableCreationDate()
    {
        $date = new \DateTime("2018-01-28 23:00:00", new \DateTimeZone("Europe/Madrid"));
        $measurement = MeasurementDataBuilder::aMeasurement()->withDate($date)->build();
        $measurement->dateCreated()->modify("+1 day");

        $this->assertEquals(
            $date->format(DATE_ISO8601),
            $measurement->dateCreated()->format(DATE_ISO8601),
            'The returned date should be a new object'
        );
    }
}
