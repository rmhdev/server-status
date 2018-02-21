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
use ServerStatus\Domain\Model\User\UserRepository;

class ViewChecksByUserService
{
    private $userRepository;
    private $transformer;

    public function __construct(UserRepository $userRepository, UserChecksDataTransformer $transformer)
    {
        $this->userRepository = $userRepository;
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
        $this->transformer->write($user);

        return $this->transformer->read();
    }
}
