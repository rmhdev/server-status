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
use ServerStatus\Domain\Model\Common\DateRange\DateRangeCustom;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatus;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;
use ServerStatus\Tests\Domain\Model\Alert\AlertTimeWindowDataBuilder;
use ServerStatus\Tests\Domain\Model\Alert\Reason\AlertReasonDowntimeDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementIdDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementResultDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementStatusDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\Percentile\PercentDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\Percentile\PercentileDataBuilder;

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
    public function itShouldAllowAddingMultipleMeasurements()
    {
        $id = MeasurementIdDataBuilder::aMeasurementId()->build();
        $measurement = MeasurementDataBuilder::aMeasurement()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add([
            MeasurementDataBuilder::aMeasurement()->build(),
            $measurement,
            MeasurementDataBuilder::aMeasurement()->build(),
        ]);

        $this->assertEquals($measurement, $repository->ofId($id));
    }

    /**
     * @test
     * @dataProvider addIncorrectObjects
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionWhenAddingNonMeasurements($incorrectValue)
    {
        $this->createEmptyRepository()->add($incorrectValue);
    }

    public function addIncorrectObjects()
    {
        return [
            [new \DateTimeImmutable("2018-02-03T00:00:00+0200")],
            ["hello"]
        ];
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

        $this->assertSame("qwerty", $repository->ofId($id)->id()->id());
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
    public function itShouldBeAbleToRemoveAnExistingValueById()
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
        $sameMinuteSummaries = $repository->summaryValues(
            $check,
            new DateRangeCustom(
                new \DateTimeImmutable("2018-02-03T00:00:10+0200"),
                new \DateTimeImmutable("2018-02-03T00:02:00+0200")
            )
        );

        $this->assertEquals(1, sizeof($sameMinuteSummaries), "items outside the date range are ignored");
        $this->assertEquals(1, $sameMinuteSummaries[0]["count"]);
    }

    /**
     * @test
     */
    public function itShouldReturnMeasurementSummaryDataGroupedByDateRangeInterval()
    {
        $repository = $this->createEmptyRepository();
        $check = CheckDataBuilder::aCheck()->build();

        // same hour measurements
        foreach (range(0, 14, 1) as $minute) {
            $repository->add(
                MeasurementDataBuilder::aMeasurement()
                    ->withCheck($check)
                    ->withDate(new \DateTime("2018-02-03T00:{$minute}:00+0200"))
                    ->build()
            );
        }
        $differentMinuteSummaries = $repository->summaryValues(
            $check,
            new DateRangeCustom(
                new \DateTime("2018-02-03T00:00:00+0200"),
                new \DateTime("2018-02-03T00:14:59+0200")
            )
        );
        $this->assertEquals(2, sizeof($differentMinuteSummaries));
        $this->assertEquals("2018-02-03 00:00:00", $differentMinuteSummaries[0]['date']);
        $this->assertEquals("2018-02-03 00:10:00", $differentMinuteSummaries[1]['date']);
    }

    /**
     * @test
     */
    public function itShouldReturnSummaryStrictlyTheDateRange()
    {
        $repository = $this->createEmptyRepository();
        $check = CheckDataBuilder::aCheck()->build();

        // same hour measurements
        foreach (range(0, 59, 1) as $minute) {
            $repository->add(
                MeasurementDataBuilder::aMeasurement()
                    ->withCheck($check)
                    ->withDate(new \DateTime("2018-02-03T00:{$minute}:50+0200"))
                    ->withResult(
                        MeasurementResultDataBuilder::aMeasurementResult()->withDuration(100)->build()
                    )->build()
            );
        }
        $summaries = $repository->summaryValues(
            $check,
            DateRangeFactory::createCustom(
                new \DateTimeImmutable("2018-02-03T00:00:50+0200"),
                new \DateTimeImmutable("2018-02-03T00:10:50+0200")
            )
        );
        $this->assertEquals(1, sizeof($summaries));
        $this->assertEquals(10, $summaries[0]["count"]);
    }

    /**
     * @test
     */
    public function itShouldCalculatePercentile()
    {
        $check = CheckDataBuilder::aCheck()->build();
        $date = new \DateTimeImmutable("2018-03-01T00:00:00+0200");
        $repository = $this->createEmptyRepository();
        $this->addMeasurementsForPercentileCalculation($repository, $check, $date);
        $dateRange = DateRangeFactory::create(DateRangeDay::NAME, $date);
        $percent = PercentDataBuilder::aPercent()
            ->withValue(0.95)
            ->build();

        $expectedPercentile = PercentileDataBuilder::aPercentile()
            ->withValue(195)
            ->withPercent($percent)
            ->build();

        $this->assertEquals(
            $expectedPercentile,
            $repository->findPercentile($check, $dateRange, $percent),
            "Calculate the {$percent} percentile for Check {$check}"
        );
    }

    private function addMeasurementsForPercentileCalculation(
        MeasurementRepository $repository,
        Check $check,
        \DateTimeImmutable $date
    ) {
        for ($i = 0; $i < 100; $i += 1) {
            $measurement = MeasurementDataBuilder::aMeasurement()
                ->withDate(
                    $date->modify(sprintf("+%d minutes", 10 * $i))
                )->withCheck($check)
                ->withResult(
                    MeasurementResultDataBuilder::aMeasurementResult()
                        ->withStatus(200)->withDuration(100 + $i)->build()
                )->build();

            $repository->add($measurement);
        }
    }

    /**
     * @test
     */
    public function itShouldCalculatePerformanceStatus()
    {
        $check = CheckDataBuilder::aCheck()->build();
        $date = new \DateTimeImmutable("2018-03-01T00:00:00+0200");
        $repository = $this->createEmptyRepository();
        foreach ($this->performanceStatusCalculationMeasurements($check, $date) as $measurement) {
            $repository->add($measurement);
        }
        $dateRange = DateRangeFactory::create(DateRangeDay::NAME, $date);
        $collection = $repository->calculatePerformanceStatus($check, $dateRange);

        /* @var PerformanceStatus $performanceStatusSuccessful */
        $performanceStatusSuccessful = $collection->filterByStatus(
            MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(200)->build()
        )->getIterator()->current();

        $this->assertEquals(200, $performanceStatusSuccessful->status()->code());
        $this->assertEquals(3, $performanceStatusSuccessful->count(), 'Number of measurements with the status');
        $this->assertEquals(105.3, $performanceStatusSuccessful->durationAverage()->value(), "average duration", 0.1);
    }

    /**
     * @param Check $check
     * @param \DateTimeImmutable $date
     * @return \Generator|Measurement[]
     */
    private function performanceStatusCalculationMeasurements(
        Check $check,
        \DateTimeImmutable $date
    ): \Generator {
        $codes = [
            200, 202, 301, 404, 100, 200, 205, 500, 404, 404, 101, 200
        ];

        foreach ($codes as $i => $code) {
            yield MeasurementDataBuilder::aMeasurement()
                ->withDate(
                    $date->modify(sprintf("+%d minutes", 10 * $i))
                )->withCheck($check)
                ->withResult(
                    MeasurementResultDataBuilder::aMeasurementResult()
                        ->withStatus($code)->withDuration(100 + $i)->build()
                )->build();
        }
    }

    /**
     * @test
     */
    public function itShouldReturnIncorrectMeasurementsByAlertWithCheckDefined()
    {
        $customer = CustomerDataBuilder::aCustomer()->build();
        $check = CheckDataBuilder::aCheck()->withCustomer($customer)->build();
        $alert = AlertDataBuilder::anAlert()
            ->withCustomer($customer)
            ->withWindow(AlertTimeWindowDataBuilder::anAlertTimeWindow()->withValue(15)->build())
            ->withReason(AlertReasonDowntimeDataBuilder::anAlertReason()->build())
            ->withCheck($check)
            ->build();
        $repository = $this->createRepositoryForFindingErrors($check);
        $result = $repository->findErrors($alert, new \DateTimeImmutable("2018-03-03T12:11:33+0200"));

        $this->assertEquals(1, $result->count(), 'It should have a single incorrect measurement by check');
    }

    private function createRepositoryForFindingErrors(Check $check): MeasurementRepository
    {
        $measurementForCheck = MeasurementDataBuilder::aMeasurement()
            ->withCheck($check)
            ->withResult(
                MeasurementResultDataBuilder::aMeasurementResult()
                    ->withStatus(404)
                    ->build()
            )->withDate(new \DateTimeImmutable("2018-03-03T12:00:00+0200"))
            ->build();

        $otherCheckByCustomer = CheckDataBuilder::aCheck()
            ->withCustomer($check->customer())
            ->build();
        $measurementForOtherCheck = MeasurementDataBuilder::aMeasurement()
            ->withCheck($otherCheckByCustomer)
            ->withResult(
                MeasurementResultDataBuilder::aMeasurementResult()
                    ->withStatus(500)
                    ->build()
            )->withDate(new \DateTimeImmutable("2018-03-03T12:05:00+0200"))
            ->build();

        $repository = $this->createEmptyRepository();
        $repository
            ->add($measurementForCheck)
            ->add($measurementForOtherCheck);

        return $repository;
    }

    /**
     * @test
     */
    public function itShouldReturnIncorrectMeasurementsByAlertWithoutCheckDefined()
    {
        $customer = CustomerDataBuilder::aCustomer()->build();
        $check = CheckDataBuilder::aCheck()->withCustomer($customer)->build();
        $alert = AlertDataBuilder::anAlert()
            ->withCustomer($customer)
            ->withWindow(AlertTimeWindowDataBuilder::anAlertTimeWindow()->withValue(15)->build())
            ->withReason(AlertReasonDowntimeDataBuilder::anAlertReason()->build())
            ->withCheck(null)
            ->build();
        $repository = $this->createRepositoryForFindingErrors($check);
        $result = $repository->findErrors($alert, new \DateTimeImmutable("2018-03-03T12:11:33+0200"));

        $this->assertEquals(2, $result->count(), 'It should have all incorrect measurements by customer');
    }

    /**
     * @test
     */
    public function itShouldReturnEmptyListWhenMeasurementsAreNotInDateRange()
    {
        $customer = CustomerDataBuilder::aCustomer()->build();
        $check = CheckDataBuilder::aCheck()->withCustomer($customer)->build();
        $alert = AlertDataBuilder::anAlert()
            ->withCustomer($customer)
            ->withWindow(AlertTimeWindowDataBuilder::anAlertTimeWindow()->withValue(15)->build())
            ->withReason(AlertReasonDowntimeDataBuilder::anAlertReason()->build())
            ->withCheck($check)
            ->build();
        $repository = $this->createRepositoryForFindingErrors($check);

        $this->assertEquals(
            0,
            $repository->findErrors($alert, new \DateTimeImmutable("2018-03-03T12:15:00+0200"))->count(),
            'There should not be measurements in date range'
        );
        $this->assertEquals(
            1,
            $repository->findErrors($alert, new \DateTimeImmutable("2018-03-03T12:14:59+0200"))->count(),
            'There should be measurements in date range'
        );
    }
}
