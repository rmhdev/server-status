<?php
declare(strict_types=1);

/**
 * This file is part of the bidaia package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Infrastructure\Domain\Model\Check;

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckFactory;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Check\CheckUrl;
use ServerStatus\Domain\Model\Customer\Customer;

class DoctrineCheckFactory implements CheckFactory
{
    public function build(CheckId $id, CheckName $name, CheckUrl $url, Customer $customer): Check
    {
        return new Check($id, $name, $url, $customer);
    }
}
