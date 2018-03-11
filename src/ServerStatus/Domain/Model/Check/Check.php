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

use Doctrine\Common\Collections\ArrayCollection;
use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Measurement\Measurement;

class Check
{
    /**
     * @var CheckId
     */
    private $id;

    /**
     * @var CheckName
     */
    private $name;

    /**
     * @var CheckUrl
     */
    private $url;

    /**
     * @var CheckStatus
     */
    private $status;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Measurement[]|ArrayCollection
     */
    private $measurements;

    /**
     * @var Alert[]|ArrayCollection
     */
    private $alerts;


    public function __construct(CheckId $id, CheckName $name, CheckUrl $url, CheckStatus $status, Customer $customer)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->status = $status;
        $this->customer = $customer;
        $this->measurements = new ArrayCollection();
        $this->alerts = new ArrayCollection();
    }

    public function id(): CheckId
    {
        return $this->id;
    }

    public function name(): CheckName
    {
        return $this->name;
    }

    public function url(): CheckUrl
    {
        return $this->url;
    }

    public function status(): CheckStatus
    {
        return $this->status;
    }

    public function customer(): Customer
    {
        return $this->customer;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s (%s)',
            $this->id(),
            $this->name()->slug()
        );
    }
}
