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

namespace ServerStatus\Infrastructure\Domain\Model\Measurement;

use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Infrastructure\Domain\Model\DoctrineEntityId;

class DoctrineMeasurementId extends DoctrineEntityId
{
    public function getName()
    {
        return "MeasurementId";
    }

    public function getNamespace()
    {
        return substr(Measurement::class, 0, -strlen("\Measurement"));
    }
}
