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

namespace ServerStatus\Tests\Infrastructure\Persistence\InMemory\Measurement;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementIdDataBuilder;

class InMemoryMeasurementRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function isShouldReturnNoResultWhenRepositoryIsEmpty()
    {
        $repo = $this->createEmptyRepository();

        $this->assertNull($repo->ofId(MeasurementIdDataBuilder::aMeasurementId()->build()));
    }

    private function createEmptyRepository(): MeasurementRepository
    {
        return new InMemoryMeasurementRepository();
    }

    /**
     * @test
     */
    public function itShouldReturnExistingMeasurementById()
    {
        $id = MeasurementIdDataBuilder::aMeasurementId()->withValue("qwerty")->build();
        $measurement = MeasurementDataBuilder::aMeasurement()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($measurement);

        $this->assertSame("qwerty", $repository->ofId($id)->id()->value());
    }

    /**
     * @test
     */
    public function itShouldReturnNoResultWhenMeasurementIsNotInRepository()
    {
        $id = MeasurementIdDataBuilder::aMeasurementId()->withValue("qwerty")->build();
        $measurement = MeasurementDataBuilder::aMeasurement()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($measurement);
        $searchById = MeasurementIdDataBuilder::aMeasurementId()->withValue("my-random-id")->build();

        $this->assertNull($repository->ofId($searchById));
    }

    /**
     * @test
     */
    public function itShouldRemoveAnExistingValueById()
    {
        $id = MeasurementIdDataBuilder::aMeasurementId()->withValue("qwerty")->build();
        $measurement = MeasurementDataBuilder::aMeasurement()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($measurement);
        $repository->remove($measurement);

        $this->assertNull($repository->ofId($id));
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Measurement\MeasurementDoesNotExistException
     */
    public function itShouldReturnExceptionWhenRemovingANonExistingMeasurement()
    {
        $id = MeasurementIdDataBuilder::aMeasurementId()->withValue("qwerty")->build();
        $measurement = MeasurementDataBuilder::aMeasurement()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($measurement);

        $newMeasurement = MeasurementDataBuilder::aMeasurement()->build();
        $repository->remove($newMeasurement);
    }

    /**
     * @test
     */
    public function itShouldReturnANewId()
    {
        $repository = $this->createEmptyRepository();

        $this->assertInstanceOf(MeasurementId::class, $repository->nextId());
    }

    /**
     * @test
     */
    public function itShouldReturnSummaryByMinuteUsingStrictlyTheDateRange()
    {
        $repository = $this->createEmptyRepository();
        $check = CheckDataBuilder::aCheck()->build();

        // same hour measurements
        foreach (range(0, 2, 1) as $minute) {
            $repository->add(
                MeasurementDataBuilder::aMeasurement()
                    ->withCheck($check)
                    ->withDate(new \DateTime("2018-02-03T00:{$minute}:05+0200"))
                    ->build()
            );
        }
        $sameMinuteSummaries = $repository->summaryByMinute(
            $check,
            new \DateTimeImmutable("2018-02-03T00:00:10+0200"),
            new \DateTimeImmutable("2018-02-03T00:02:00+0200")
        );

        $this->assertEquals(1, sizeof($sameMinuteSummaries));
        $this->assertEquals(1, $sameMinuteSummaries[0]["count"]);
    }

    /**
     * @test
     */
    public function itShouldReturnMeasurementSummaryDataGroupedByMinute()
    {
        $repository = $this->createEmptyRepository();
        $check = CheckDataBuilder::aCheck()->build();

        // same hour measurements
        foreach (range(0, 9, 1) as $minute) {
            $repository->add(
                MeasurementDataBuilder::aMeasurement()
                    ->withCheck($check)
                    ->withDate(new \DateTime("2018-02-03T00:{$minute}:00+0200"))
                    ->build()
            );
        }
        $differentMinuteSummaries = $repository->summaryByMinute(
            $check,
            new \DateTime("2018-02-03T00:00:00+0200"),
            new \DateTime("2018-02-03T00:04:59+0200")
        );
        $this->assertEquals(5, sizeof($differentMinuteSummaries), "Summary for different minutes");
        $this->assertEquals("2018-02-03 00:00:00", $differentMinuteSummaries[0]['date']);
        $this->assertEquals("2018-02-03 00:04:00", $differentMinuteSummaries[4]['date']);
    }

    /**
     * @test
     */
    public function itShouldReturnSummaryByHourUsingStrictlyTheDateRange()
    {
        $repository = $this->createEmptyRepository();
        $check = CheckDataBuilder::aCheck()->build();

        // same hour measurements
        foreach (range(0, 5, 1) as $minute) {
            $repository->add(
                MeasurementDataBuilder::aMeasurement()
                    ->withCheck($check)
                    ->withDate(new \DateTime("2018-02-03T00:{$minute}:50+0200"))
                    ->build()
            );
        }
        $summaries = $repository->summaryByHour(
            $check,
            new \DateTimeImmutable("2018-02-03T00:00:51+0200"),
            new \DateTimeImmutable("2018-02-03T00:01:49+0200")
        );
        $this->assertEquals(0, sizeof($summaries));

        $summaries = $repository->summaryByHour(
            $check,
            new \DateTimeImmutable("2018-02-03T00:00:50+0200"),
            new \DateTimeImmutable("2018-02-03T00:01:50+0200")
        );
        $this->assertEquals(1, sizeof($summaries));
        $this->assertEquals(2, $summaries[0]["count"]);
    }
}
