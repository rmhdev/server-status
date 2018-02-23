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

namespace ServerStatus\Infrastructure\Persistence\InMemory\Check;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckIdDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;

class InMemoryCheckRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function isShouldReturnNoResultWhenRepositoryIsEmpty()
    {
        $repo = $this->createEmptyRepository();

        $this->assertNull($repo->ofId(CheckIdDataBuilder::aCheckId()->build()));
    }

    private function createEmptyRepository(): CheckRepository
    {
        return new InMemoryCheckRepository();
    }

    /**
     * @test
     */
    public function itShouldReturnExistingCheckById()
    {
        $id = CheckIdDataBuilder::aCheckId()->withValue("qwerty")->build();
        $check = CheckDataBuilder::aCheck()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($check);

        $this->assertSame("qwerty", $repository->ofId($id)->id()->value());
    }

    /**
     * @test
     */
    public function itShouldReturnNoResultWhenMeasurementIsNotInRepository()
    {
        $id = CheckIdDataBuilder::aCheckId()->withValue("qwerty")->build();
        $check = CheckDataBuilder::aCheck()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($check);
        $searchById = CheckIdDataBuilder::aCheckId()->withValue("my-random-id")->build();

        $this->assertNull($repository->ofId($searchById));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToRemoveAnExistingValueById()
    {
        $id = CheckIdDataBuilder::aCheckId()->withValue("qwerty")->build();
        $check = CheckDataBuilder::aCheck()->withId($id)->build();
        $repository = $this
            ->createEmptyRepository()
            ->add($check)
            ->remove($check)
        ;

        $this->assertNull($repository->ofId($id));
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Check\CheckDoesNotExistException
     */
    public function itShouldReturnExceptionWhenRemovingANonExistingMeasurement()
    {
        $id = CheckIdDataBuilder::aCheckId()->withValue("qwerty")->build();
        $check = CheckDataBuilder::aCheck()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($check);

        $new = CheckDataBuilder::aCheck()->build();
        $repository->remove($new);
    }

    /**
     * @test
     */
    public function itShouldReturnANewId()
    {
        $repository = $this->createEmptyRepository();

        $this->assertInstanceOf(CheckId::class, $repository->nextId());
    }

    /**
     * @test
     */
    public function isShouldReturnChecksForAGivenCustomer()
    {
        $customerId = CustomerIdDataBuilder::aCustomerId()->withValue("first")->build();
        $customer = CustomerDataBuilder::aCustomer()->withId($customerId)->build();
        $otherCustomerId = CustomerIdDataBuilder::aCustomerId()->withValue("other")->build();
        $otherCustomer = CustomerDataBuilder::aCustomer()->withId($otherCustomerId)->build();
        $repository = $this->createEmptyRepository();
        $repository
            ->add(CheckDataBuilder::aCheck()->withCustomer($customer)->build())
            ->add(CheckDataBuilder::aCheck()->withCustomer($otherCustomer)->build())
            ->add(CheckDataBuilder::aCheck()->withCustomer($otherCustomer)->build())
        ;
        $customerCollection = $repository->byCustomer($customerId);

        $this->assertEquals(1, $customerCollection->count(), 'Customer "first" should have one Check');
        $this->assertTrue(
            $customerCollection->getIterator()->current()->customer()->id()->equals($customerId),
            'Check should be related to Customer "first"'
        );
        $this->assertEquals(
            2,
            $repository->byCustomer($otherCustomerId)->count(),
            'Customer "other" should have two Checks'
        );
    }
}
