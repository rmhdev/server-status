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

namespace ServerStatus\Domain\Model\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\Check\Check;

class Customer
{
    /**
     * @var CustomerId
     */
    private $id;

    /**
     * @var CustomerEmail
     */
    protected $email;

    /**
     * @var CustomerAlias
     */
    private $alias;

    /**
     * @var CustomerStatus
     */
    private $status;

    /**
     * @var Check[]|ArrayCollection
     */
    private $checks;

    /**
     * @var Alert[]|ArrayCollection
     */
    private $alerts;


    public function __construct(CustomerId $id, CustomerEmail $email, CustomerAlias $alias, CustomerStatus $status)
    {
        $this->id = $id;
        $this->email = $email;
        $this->alias = $alias;
        $this->status = $status;
        $this->checks = new ArrayCollection();
        $this->alerts = new ArrayCollection();
    }

    public function id(): CustomerId
    {
        return $this->id;
    }

    public function email(): CustomerEmail
    {
        return $this->email;
    }

    public function alias(): CustomerAlias
    {
        return $this->alias;
    }

    public function status(): CustomerStatus
    {
        return $this->status;
    }

    public function screenName(): string
    {
        return $this->alias()->isEmpty() ? $this->email()->value() : $this->alias()->value();
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->email(), $this->id());
    }
}
