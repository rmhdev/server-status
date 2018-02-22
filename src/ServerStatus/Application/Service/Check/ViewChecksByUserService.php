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

namespace ServerStatus\Application\Service\Check;

use ServerStatus\Application\DataTransformer\User\UserChecksDataTransformer;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryCollection;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryFactory;
use ServerStatus\Domain\Model\User\UserRepository;

class ViewChecksByUserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CheckRepository
     */
    private $checkRepository;

    /**
     * @var MeasurementRepository
     */
    private $measurementRepository;

    /**
     * @var UserChecksDataTransformer
     */
    private $transformer;


    public function __construct(
        UserRepository $userRepository,
        CheckRepository $checkRepository,
        MeasurementRepository $measurementRepository,
        UserChecksDataTransformer $transformer
    ) {
        $this->userRepository = $userRepository;
        $this->checkRepository = $checkRepository;
        $this->measurementRepository = $measurementRepository;
        $this->transformer = $transformer;
    }

    public function execute(ViewChecksByUserRequest $request = null)
    {
        if (is_null($request)) {
            return [];
        }
        $user = $this->userRepository->ofId($request->userId());
        if (!$user) {
            return [];
        }
        $checkCollection = $this->checkRepository->byUser($request->userId());

        $summaries = [];
        foreach ($checkCollection as $check) {
            $summaries[] = MeasureSummaryFactory::create(
                $request->name(),
                $check,
                $this->measurementRepository,
                $request->date()
            );
        }
        $this->transformer->write(
            $user,
            $checkCollection,
            new MeasureSummaryCollection($summaries)
        );

        return $this->transformer->read();
    }
}
