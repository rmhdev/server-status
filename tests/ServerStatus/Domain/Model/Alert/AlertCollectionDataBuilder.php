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

namespace ServerStatus\Tests\Domain\Model\Alert;

use ServerStatus\Domain\Model\Alert\AlertCollection;

class AlertCollectionDataBuilder
{
    private $alerts;

    public function __construct()
    {
        $this->alerts = [];
    }

    public function withAlerts($alerts = []): AlertCollectionDataBuilder
    {
        $this->alerts = $alerts;

        return $this;
    }

    public function build(): AlertCollection
    {
        return new AlertCollection($this->alerts);
    }

    public static function anAlertCollection(): AlertCollectionDataBuilder
    {
        return new self();
    }
}
