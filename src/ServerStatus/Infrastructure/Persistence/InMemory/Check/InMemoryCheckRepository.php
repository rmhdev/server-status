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

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckAlreadyExistException;
use ServerStatus\Domain\Model\Check\CheckDoesNotExistException;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Check\CheckCollection;

class InMemoryCheckRepository implements CheckRepository
{
    private $checks;

    public function __construct()
    {
        $this->checks = [];
    }

    /**
     * @return Check[]
     */
    private function checks(): array
    {
        return $this->checks;
    }

    /**
     * @inheritdoc
     */
    public function ofId(CheckId $id): ?Check
    {
        if (array_key_exists($id->id(), $this->checks)) {
            return $this->checks[$id->id()];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function add(Check $check): CheckRepository
    {
        if ($this->byCustomerAndSlug($check->customer()->id(), $check->name())) {
            throw new CheckAlreadyExistException(sprintf(
                'Customer "%s" already has a check with same slug "%s" (id "%s")',
                $check->customer()->id(),
                $check->name()->slug(),
                $check->id()
            ));
        }
        $this->checks[$check->id()->id()] = $check;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(Check $check): CheckRepository
    {
        $id = $check->id()->id();
        if (!array_key_exists($id, $this->checks)) {
            throw new CheckDoesNotExistException(sprintf(
                'Check "%s" cannot be removed from "in memory" repository',
                $id
            ));
        }
        unset($this->checks[$id]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function nextId(): CheckId
    {
        return new CheckId();
    }

    /**
     * @inheritdoc
     */
    public function byCustomer(CustomerId $id): CheckCollection
    {
        $checks = $this->checks();

        return new CheckCollection(
            array_filter($checks, function (Check $check) use ($id) {
                return $check->customer()->id()->equals($id);
            })
        );
    }

    public function byCustomerAndSlug(CustomerId $id, CheckName $slug): ?Check
    {
        $checks = $this->checks();
        $collection = new CheckCollection(
            array_filter($checks, function (Check $check) use ($id, $slug) {
                return $check->customer()->id()->equals($id) && $check->name()->slug() == $slug->slug();
            })
        );
        if (1 != $collection->count()) {
            return null;
        }

        return $collection->getIterator()->current();
    }

    /**
     * @inheritdoc
     */
    public function enabled(): CheckCollection
    {
        $checks = $this->checks();

        return new CheckCollection(
            array_filter($checks, function (Check $check) {
                if (!$check->status()->isEnabled()) {
                    return false;
                }
                return $check->customer()->status()->isEnabled();
            })
        );
    }
}
