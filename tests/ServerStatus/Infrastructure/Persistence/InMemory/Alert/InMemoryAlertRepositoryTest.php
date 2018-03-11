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

namespace ServerStatus\Infrastructure\Persistence\InMemory\Alert;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Alert\AlertId;
use ServerStatus\Domain\Model\Alert\AlertRepository;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;
use ServerStatus\Tests\Domain\Model\Alert\AlertIdDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;

class InMemoryAlertRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function isShouldReturnNoResultWhenRepositoryIsEmpty()
    {
        $repo = $this->createEmptyRepository();

        $this->assertNull($repo->ofId(AlertIdDataBuilder::anAlertId()->build()));
    }

    private function createEmptyRepository(): AlertRepository
    {
        return new InMemoryAlertRepository();
    }

    /**
     * @test
     */
    public function itShouldReturnExistingAlertById()
    {
        $id = AlertIdDataBuilder::anAlertId()->withValue("qwerty")->build();
        $alert = AlertDataBuilder::anAlert()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($alert);

        $this->assertSame("qwerty", $repository->ofId($id)->id()->id());
    }

    /**
     * @test
     */
    public function itShouldReturnNoResultWhenAlertIsNotInRepository()
    {
        $id = AlertIdDataBuilder::anAlertId()->withValue("qwerty")->build();
        $alert = AlertDataBuilder::anAlert()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($alert);
        $searchById = AlertIdDataBuilder::anAlertId()->withValue("my-random-id")->build();

        $this->assertNull($repository->ofId($searchById));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToRemoveAnExistingValueById()
    {
        $id = AlertIdDataBuilder::anAlertId()->withValue("qwerty")->build();
        $alert = AlertDataBuilder::anAlert()->withId($id)->build();
        $repository = $this
            ->createEmptyRepository()
            ->add($alert)
            ->remove($alert)
        ;

        $this->assertNull($repository->ofId($id));
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Alert\AlertDoesNotExistException
     */
    public function itShouldThrowExceptionWhenRemovingANonExistingAlert()
    {
        $id = AlertIdDataBuilder::anAlertId()->withValue("qwerty")->build();
        $alert = AlertDataBuilder::anAlert()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($alert);

        $new = AlertDataBuilder::anAlert()->build();
        $repository->remove($new);
    }

    /**
     * @test
     */
    public function itShouldReturnANewId()
    {
        $repository = $this->createEmptyRepository();

        $this->assertInstanceOf(AlertId::class, $repository->nextId());
    }

    /**
     * @test
     */
    public function isShouldReturnAlertsForAGivenCustomer()
    {
        $customerId = CustomerIdDataBuilder::aCustomerId()->withValue("first")->build();
        $customer = CustomerDataBuilder::aCustomer()->withId($customerId)->build();
        $otherCustomerId = CustomerIdDataBuilder::aCustomerId()->withValue("other")->build();
        $otherCustomer = CustomerDataBuilder::aCustomer()->withId($otherCustomerId)->build();
        $repository = $this->createEmptyRepository();
        $repository
            ->add(AlertDataBuilder::anAlert()->withCustomer($customer)->build())
            ->add(AlertDataBuilder::anAlert()->withCustomer($otherCustomer)->build())
            ->add(AlertDataBuilder::anAlert()->withCustomer($otherCustomer)->build())
        ;
        $collection = $repository->byCustomer($customerId);

        $this->assertEquals(1, $collection->count(), 'Customer "first" should have one Alert');
        $this->assertTrue(
            $collection->getIterator()->current()->customer()->id()->equals($customerId),
            'Check should be related to Customer "first"'
        );
        $this->assertEquals(
            2,
            $repository->byCustomer($otherCustomerId)->count(),
            'Customer "other" should have two Alerts'
        );
    }
}
