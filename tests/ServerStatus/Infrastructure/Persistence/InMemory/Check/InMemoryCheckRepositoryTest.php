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
use ServerStatus\Domain\Model\Check\CheckStatus;
use ServerStatus\Domain\Model\Customer\CustomerStatus;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckIdDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckNameDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckStatusDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerStatusDataBuilder;

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

        $this->assertSame("qwerty", $repository->ofId($id)->id()->id());
    }

    /**
     * @test
     */
    public function itShouldReturnNoResultWhenCheckIsNotInRepository()
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
    public function itShouldThrowExceptionWhenRemovingANonExistingCheck()
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
            ->add(CheckDataBuilder::aCheck()->withCustomer($customer)->withName("first")->build())
            ->add(CheckDataBuilder::aCheck()->withCustomer($otherCustomer)->withName("second")->build())
            ->add(CheckDataBuilder::aCheck()->withCustomer($otherCustomer)->withName("third")->build())
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

    /**
     * @test
     */
    public function itShouldReturnEnabledChecks()
    {
        $customerEnabled = CustomerDataBuilder::aCustomer()->withStatus(
            CustomerStatusDataBuilder::aCustomerStatus()->withValue(CustomerStatus::CODE_ENABLED)->build()
        )->build();
        $customerDisabled = CustomerDataBuilder::aCustomer()->withStatus(
            CustomerStatusDataBuilder::aCustomerStatus()->withValue(CustomerStatus::CODE_DISABLED)->build()
        )->build();
        $statusDisabled = CheckStatusDataBuilder::aCheckStatus()->withCode(CheckStatus::CODE_DISABLED)->build();

        $repository = $this->createEmptyRepository();
        $repository
            ->add(CheckDataBuilder::aCheck()->withCustomer($customerEnabled)->withName("first")->build())
            ->add(CheckDataBuilder::aCheck()->withCustomer($customerEnabled)->withName("second")
                ->withStatus($statusDisabled)->build())
            ->add(CheckDataBuilder::aCheck()->withCustomer($customerDisabled)->withName("third")->build())
        ;

        $this->assertEquals(1, $repository->enabled()->count(), 'Return only enabled checks from enabled customers');
    }

    /**
     * @test
     */
    public function itShouldReturnEmptyByCustomerAndSlugWhenCheckIsNotRelatedToCustomer()
    {
        $customer = CustomerDataBuilder::aCustomer()->build();
        $repository = $this->createEmptyRepository();
        $repository
            ->add(CheckDataBuilder::aCheck()->withName("check-one")->withCustomer($customer)->build())
            ->add(CheckDataBuilder::aCheck()->withName("check-two")->build())
        ;

        $this->assertNull($repository->byCustomerAndSlug(
            $customer->id(),
            CheckNameDataBuilder::aCheckName()->withName("check-two")->withSlug("check-two")->build()
        ));
    }

    /**
     * @test
     * @expectedException  \ServerStatus\Domain\Model\Check\CheckAlreadyExistException
     */
    public function itShouldThrowExceptionWhenAddingWithSameSlugForSameCustomer()
    {
        $id = CheckIdDataBuilder::aCheckId()->withValue("qwerty")->build();
        $checkA = CheckDataBuilder::aCheck()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($checkA);
        $checkB = CheckDataBuilder::aCheck()
            ->withName($checkA->name())
            ->withCustomer($checkA->customer())
            ->build();
        $repository->add($checkB);
    }
}
