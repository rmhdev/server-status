<?php
declare(strict_types=1);

/**
 * This file is part of the server-status package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Application\Service\Check;

use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Infrastructure\Domain\Model\Check\DoctrineCheckFactory;

class AddCheckService
{
    /**
     * @var CheckRepository
     */
    private $repository;


    public function __construct(CheckRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(AddCheckRequest $request): void
    {
        $factory = new DoctrineCheckFactory();
        $this->repository->add(
            $factory->build(
                new CheckId(),
                $request->name(),
                $request->url(),
                $request->status(),
                $request->customer()
            )
        );
    }
}
