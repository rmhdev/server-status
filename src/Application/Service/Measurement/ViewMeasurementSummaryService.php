<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Application\Service\Measurement;

use ServerStatus\Application\DataTransformer\Measurement\MeasurementSummaryDataTransformer;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;

class ViewMeasurementSummaryService
{
    private $repository;

    private $transformer;

    public function __construct(MeasurementRepository $repository, MeasurementSummaryDataTransformer $transformer)
    {
        $this->repository = $repository;
        $this->transformer = $transformer;
    }

    /**
     * @param ViewLastDayMeasurementSummaryRequest $request
     * @return array
     */
    public function execute($request = null)
    {
        if (!$request) {
            return [];
        }
        $this->transformer->write(
            $this->repository->summaryByMinute($request->check(), $request->from(), $request->to())
        );

        return $this->transformer->read();
    }
}
