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

namespace ServerStatus\Infrastructure\Domain\Model\Alert;

use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Infrastructure\Domain\Model\DoctrineEntityId;

class DoctrineAlertId extends DoctrineEntityId
{
    public function getName()
    {
        return "AlertId";
    }

    public function getNamespace()
    {
        return substr(Alert::class, 0, -strlen("\Alert"));
    }
}
