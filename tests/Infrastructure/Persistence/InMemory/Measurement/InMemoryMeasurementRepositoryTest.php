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
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
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
}
