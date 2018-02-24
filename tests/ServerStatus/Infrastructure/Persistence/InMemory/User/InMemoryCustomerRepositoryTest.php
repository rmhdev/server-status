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

namespace ServerStatus\Tests\Infrastructure\Persistence\InMemory\Customer;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryCustomerRepository;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;

class InMemoryCustomerRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function isShouldReturnNoResultWhenRepositoryIsEmpty()
    {
        $repo = $this->createEmptyRepository();

        $this->assertNull($repo->ofId(CustomerIdDataBuilder::aCustomerId()->build()));
    }

    private function createEmptyRepository(): CustomerRepository
    {
        return new InMemoryCustomerRepository();
    }

    /**
     * @test
     */
    public function itShouldReturnExistingUserById()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->withValue("qwerty")->build();
        $customer = CustomerDataBuilder::aCustomer()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($customer);

        $this->assertSame("qwerty", $repository->ofId($id)->id()->id());
    }

    /**
     * @test
     */
    public function itShouldReturnNoResultWhenUserIsNotInRepository()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->withValue("qwerty")->build();
        $customer = CustomerDataBuilder::aCustomer()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($customer);
        $searchById = CustomerIdDataBuilder::aCustomerId()->withValue("other-id")->build();

        $this->assertNull($repository->ofId($searchById));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToRemoveAnExistingValueById()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->withValue("qwerty")->build();
        $customer = CustomerDataBuilder::aCustomer()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($customer);
        $repository->remove($customer);

        $this->assertNull($repository->ofId($id));
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException
     */
    public function itShouldReturnExceptionWhenRemovingANonExistingUser()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->withValue("qwerty")->build();
        $customer = CustomerDataBuilder::aCustomer()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($customer);

        $new = CustomerDataBuilder::aCustomer()->build();
        $repository->remove($new);
    }

    /**
     * @test
     */
    public function itShouldReturnANewId()
    {
        $repository = $this->createEmptyRepository();

        $this->assertInstanceOf(CustomerId::class, $repository->nextId());
    }
}
