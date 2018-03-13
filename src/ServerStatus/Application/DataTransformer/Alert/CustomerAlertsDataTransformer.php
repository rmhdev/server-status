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

namespace ServerStatus\Application\DataTransformer\Alert;

use ServerStatus\Domain\Model\AlertNotification\Log\AlertNotificationsLogCollection;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Customer\Customer;

interface CustomerAlertsDataTransformer
{
    public function write(Customer $customer, DateRange $dateRange, AlertNotificationsLogCollection $logCollection);

    public function read();
}
