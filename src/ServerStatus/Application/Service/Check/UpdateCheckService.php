<?php
declare(strict_types=1);

/**
 * This file is part of the server-status package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Application\Service\Check;

use ServerStatus\Domain\Model\Check\CheckDoesNotExistException;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Infrastructure\Domain\Model\Check\DoctrineCheckFactory;

class UpdateCheckService
{
    private $checkRepository;

    public function __construct(CheckRepository $checkRepository)
    {
        $this->checkRepository = $checkRepository;
    }

    /**
     * @param UpdateCheckRequest $request
     * @throws \ServerStatus\Domain\Model\Check\CheckDoesNotExistException
     * @throws \ServerStatus\Domain\Model\Check\CheckAlreadyExistException
     */
    public function execute(UpdateCheckRequest $request)
    {
        $check = $this->checkRepository->ofId($request->id());
        if (!$check) {
            throw new CheckDoesNotExistException(sprintf(
                'Update action error: Check object (id "%s") does not exist in repository',
                $request->id()
            ));
        }
        $factory = new DoctrineCheckFactory();
        $edited = $factory->build(
            $request->id(),
            $request->name(),
            $request->url(),
            $request->status(),
            $check->customer()
        );
        $this->checkRepository->add($edited);
    }
}
