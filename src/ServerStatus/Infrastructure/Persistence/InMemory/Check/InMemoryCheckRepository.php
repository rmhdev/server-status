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
use ServerStatus\Domain\Model\Check\CheckDoesNotExistException;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\ServerStatus\Domain\Model\Check\CheckCollection;

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
        if (array_key_exists($id->value(), $this->checks)) {
            return $this->checks[$id->value()];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function add(Check $check): CheckRepository
    {
        $this->checks[$check->id()->value()] = $check;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(Check $check): CheckRepository
    {
        $id = $check->id()->value();
        if (!array_key_exists($id, $this->checks)) {
            throw new CheckDoesNotExistException(sprintf(
                'Check with id "%s" does not exist',
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
}
