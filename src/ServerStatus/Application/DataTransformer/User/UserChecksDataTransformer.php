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

namespace ServerStatus\Application\DataTransformer\User;

use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryCollection;
use ServerStatus\ServerStatus\Domain\Model\Check\CheckCollection;
use ServerStatus\ServerStatus\Domain\Model\User\User;

interface UserChecksDataTransformer
{
    public function write(User $user, CheckCollection $checkCollection, MeasureSummaryCollection $measureSummaries);

    public function read();
}
