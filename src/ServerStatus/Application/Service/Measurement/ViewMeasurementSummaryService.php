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
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummary;

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
     * @param ViewMeasurementSummaryRequest $request
     * @return array
     */
    public function execute($request = null)
    {
        if (!$request) {
            return [];
        }
        $measureSummary = new MeasureSummary(
            $request->check(),
            $this->repository->summaryValues(
                $request->check(),
                $request->dateRange()
            ),
            $request->dateRange()
        );
        $this->transformer->write($measureSummary);

        return $this->transformer->read();
    }
}
