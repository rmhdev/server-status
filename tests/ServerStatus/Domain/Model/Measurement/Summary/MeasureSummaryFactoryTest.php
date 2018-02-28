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

namespace ServerStatus\Tests\Domain\Model\Measurement\Summary;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummary;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryFactory;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;

class MeasureSummaryFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider measureSummaryDataProvider
     */
    public function itShouldGenerateAMeasureSummaryByName($name, $className)
    {
        $measureSummary = MeasureSummaryFactory::create(
            CheckDataBuilder::aCheck()->build(),
            new InMemoryMeasurementRepository(),
            DateRangeFactory::create($name, new \DateTimeImmutable("2018-02-02T15:24:10+0200"))
        );

        $this->assertEquals($name, $measureSummary->name());
        $this->assertInstanceOf($className, $measureSummary);
    }

    public function measureSummaryDataProvider()
    {
        return [
            ["day", MeasureSummary::class],
        ];
    }

    /**
     * @test
     * @dataProvider incorrectMeasurementSummaryNames
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionWithIncorrectNames($incorrectName)
    {
        MeasureSummaryFactory::create(
            CheckDataBuilder::aCheck()->build(),
            new InMemoryMeasurementRepository(),
            DateRangeFactory::create($incorrectName, new \DateTimeImmutable("2018-02-02T15:24:10+0200"))
        );
    }

    public function incorrectMeasurementSummaryNames()
    {
        return [
            [""],
            ["test"]
        ];
    }
}
