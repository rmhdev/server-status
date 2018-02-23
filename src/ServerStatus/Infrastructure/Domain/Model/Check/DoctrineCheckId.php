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
use ServerStatus\Infrastructure\Domain\Model\DoctrineEntityId;

class DoctrineCheckId extends DoctrineEntityId
{
    public function getName()
    {
        return "CheckId";
    }

    public function getNamespace()
    {
        return substr(Check::class, 0, -strlen("\Check"));
    }
}
