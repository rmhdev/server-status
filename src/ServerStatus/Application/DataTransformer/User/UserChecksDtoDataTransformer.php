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

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummary;
use ServerStatus\ServerStatus\Domain\Model\Check\CheckCollection;
use ServerStatus\ServerStatus\Domain\Model\User\User;

final class UserChecksDtoDataTransformer implements UserChecksDataTransformer
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var CheckCollection
     */
    private $checkCollection;

    /**
     * @var MeasureSummary
     */
    private $measureSummaries;

    /**
     * TODO: introducir de alguna manera la lista de measureSummaries en cada "check".
     * IDEA: un MeasureSummaryCollection? que permita filtrar por check.
     */
    public function write(User $user, CheckCollection $checkCollection, $measureSummaries = [])
    {
        $this->user = $user;
        $this->checkCollection = $checkCollection;
        $this->measureSummaries = $measureSummaries;
    }

    public function read()
    {
        return [
            "user" => $this->processUser(),
            "checks" => $this->processChecks(),
        ];
    }

    private function processUser(): array
    {
        return [
            "id" => (string) $this->user->id(),
            "alias" => (string) $this->user->alias(),
        ];
    }

    private function processChecks(): array
    {
        $values = [];
        foreach ($this->checkCollection as $check) {
            $values[] = $this->processCheck($check);
        }

        return $values;
    }

    private function processCheck(Check $check): array
    {
        return [
            "id" => (string) $check->id(),
            "name" => (string) $check->name()->value(),
            "slug" => (string) $check->name()->slug(),
        ];
    }

    private function processMeasureSummary(): array
    {
        if (!$this->measureSummary) {
            return [];
        }
        return [
            "name" => $this->measureSummary->name(),
        ];
    }
}
