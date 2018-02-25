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

namespace ServerStatus\Domain\Model\Check;

final class CheckCollection implements \Countable, \IteratorAggregate
{
    private $checks;

    public function __construct($checks = [])
    {
        $this->checks = $this->processChecks($checks);
    }

    private function processChecks($checks = [])
    {
        $checks = is_array($checks) ? $checks : [$checks];
        $iterator = new \ArrayIterator();
        foreach ($checks as $check) {
            $this->assertCheck($check);
            $iterator->append($check);
        }

        return $iterator;
    }

    private function assertCheck($check)
    {
        if (!is_object($check) || !$check instanceof Check) {
            throw new \UnexpectedValueException(sprintf(
                'CheckCollection only accepts "Check" objects, "%s" received',
                gettype($check)
            ));
        }
    }

    private function checks(): \ArrayIterator
    {
        return $this->checks;
    }

    public function count(): int
    {
        return $this->checks()->count();
    }

    /**
     * @return \ArrayIterator|Check[]
     */
    public function getIterator(): \ArrayIterator
    {
        return $this->checks();
    }

    /**
     * @return CheckUrl[]
     */
    public function checkUrls(): array
    {
        return array_values(
            array_unique(
                array_map(
                    function (Check $check) {
                        return $check->url();
                    },
                    $this->checks()->getArrayCopy()
                ),
                SORT_STRING
            )
        );
    }

    /**
     * @param CheckUrl $url
     * @return Check[]
     */
    public function byCheckUrl(CheckUrl $url): array
    {
        return array_values(
            array_filter(
                $this->checks()->getArrayCopy(),
                function (Check $check) use ($url) {
                    return $check->url()->equals($url);
                }
            )
        );
    }
}
