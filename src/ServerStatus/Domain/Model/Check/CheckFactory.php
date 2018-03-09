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

use ServerStatus\Domain\Model\Customer\Customer;

interface CheckFactory
{
    public function build(CheckId $id, CheckName $name, CheckUrl $url, CheckStatus $status, Customer $customer): Check;
}
