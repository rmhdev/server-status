<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Tests\Application\Service\Measurement;

use PHPUnit\Framework\TestCase;
use ServerStatus\Application\DataTransformer\Measurement\MeasurementSummaryDtoDataTransformer;
use ServerStatus\Application\Service\Measurement\ViewDayMeasurementSummaryRequest;
use ServerStatus\Application\Service\Measurement\ViewMeasurementSummaryService;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;

class ViewMeasurementSummaryServiceTest extends TestCase
{
    private $repository;
    private $transformer;
    private $check;

    protected function setUp()
    {
        parent::setUp();
        $repo = new InMemoryMeasurementRepository();

        $check = CheckDataBuilder::aCheck()->build();
        $this->check = $check;

        // same hour measurements
        foreach (range(0, 55, 5) as $minute) {
            $repo->add($this->createMeasurement($check, new \DateTime("2018-02-03T00:{$minute}:00+0200")));
        }
        // same day measurements
        foreach (range(1, 23, 1) as $hour) {
            $repo->add($this->createMeasurement($check, new \DateTime("2018-02-03T{$hour}:05:00+0200")));
        }
        $this->repository = $repo;
        $this->transformer = new MeasurementSummaryDtoDataTransformer();
    }

    private function createMeasurement(Check $check, \DateTimeInterface $date): Measurement
    {
        return MeasurementDataBuilder::aMeasurement()->withCheck($check)->withDate($date)->build();
    }

    protected function tearDown()
    {
        unset($this->repository);
        unset($this->transformer);
        unset($this->check);

        parent::tearDown();
    }

    /**
     * @test
     */
    public function isShouldReturnEmptyListWhenNoRequestIsGiven()
    {
        $service = new ViewMeasurementSummaryService($this->repository, $this->transformer);

        $this->assertEquals([], $service->execute());
    }

    /**
     * @test
     */
    public function isShouldReturnAnExactNumberOfValuesForDaySummary()
    {
        $request = new ViewDayMeasurementSummaryRequest(
            $this->check,
            DateRangeFactory::create(DateRangeDay::NAME, new \DateTimeImmutable("2018-02-03T23:59:59+0200"))
        );

        $service = new ViewMeasurementSummaryService($this->repository, $this->transformer);
        $summary = $service->execute($request);

        $this->assertEquals(24 * 6, sizeof($summary["averages"]));
    }
}
