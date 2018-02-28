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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function itShouldBeIterable()
    {
        $collection = $this->createCollection();

        $this->assertTrue(is_iterable($collection));
    }

    /**
     * @test
     */
    public function itShouldBeImmutable()
    {
        $collection = $this->createCollection([]);
        $iterator = $collection->getIterator();
        if (method_exists($iterator, 'append')) {
            /* @var \ArrayIterator $iterator */
            $iterator->append(
                CheckDataBuilder::aCheck()->build()
            );
            $this->assertEquals(0, $collection->count(), 'Appending new objects should not change the collection');
        } else {
            $this->assertTrue(true, 'Iterator has not an append method');
        }
    }

    /**
     * @test
     */
    public function itShouldReturnUniqueCheckUrls()
    {
        $urlA = CheckUrlDataBuilder::aCheckUrl()->withDomain("www.example.com")->build();
        $urlB = CheckUrlDataBuilder::aCheckUrl()->withDomain("site.example.net")->build();
        $collection = $this->createCollection([
            CheckDataBuilder::aCheck()->withUrl($urlA)->build(),
            CheckDataBuilder::aCheck()->withUrl($urlA)->build(),
            CheckDataBuilder::aCheck()->withUrl($urlB)->build(),
        ]);

        $this->assertEquals([$urlA, $urlB], $collection->checkUrls());
    }

    /**
     * @test
     */
    public function itShouldReturnChecksWithAGivenUrl()
    {
        $urlA = CheckUrlDataBuilder::aCheckUrl()->withDomain("example.io")->build();
        $urlB = CheckUrlDataBuilder::aCheckUrl()->withDomain("site.example.net")->build();
        $check1 = CheckDataBuilder::aCheck()->withUrl($urlA)->build();
        $check2 = CheckDataBuilder::aCheck()->withUrl($urlB)->build();
        $check3 = CheckDataBuilder::aCheck()->withUrl($urlA)->build();
        $collection = $this->createCollection([$check1, $check2, $check3]);

        $this->assertEquals(new CheckCollection([$check1, $check3]), $collection->byCheckUrl($urlA));
        $this->assertEquals(new CheckCollection([$check2]), $collection->byCheckUrl($urlB));

        $otherUrl = CheckUrlDataBuilder::aCheckUrl()->build();
        $this->assertEquals(
            new CheckCollection([]),
            $collection->byCheckUrl($otherUrl),
            "Url is not defined in collection"
        );
    }
}
