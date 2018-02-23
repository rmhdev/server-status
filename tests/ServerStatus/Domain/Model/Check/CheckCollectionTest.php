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

namespace ServerStatus\Tests\Domain\Model\Check;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Check\CheckCollection;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;

class CheckCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAcceptAnEmptyListOfChecks()
    {
        $collection = $this->createCollection();

        $this->assertEquals(0, $collection->count());
    }

    private function createCollection($checks = []): CheckCollection
    {
        return CheckCollectionDataBuilder::aCheckCollection()->withChecks($checks)->build();
    }

    /**
     * @test
     */
    public function itShouldCountChecks()
    {
        $collection = $this->createCollection([
            CheckDataBuilder::aCheck()->build(),
            CheckDataBuilder::aCheck()->build(),
            CheckDataBuilder::aCheck()->build(),
        ]);

        $this->assertEquals(3, $collection->count());
    }

    public function itShouldAcceptASingleCheck()
    {
        $collection = $this->createCollection(CheckDataBuilder::aCheck()->build());

        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     * @dataProvider incorrectItemDataProvider
     */
    public function itShouldThrowExceptionIfSomethingDifferentToCheckIsUSed($item)
    {
        $this->createCollection($item);
    }

    public function incorrectItemDataProvider()
    {
        return [
            [CustomerDataBuilder::aCustomer()->build()],
            ["hello"],
        ];
    }
}
