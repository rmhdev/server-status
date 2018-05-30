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
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Check\CheckStatus;
use ServerStatus\Domain\Model\Check\CheckUrl;

class UpdateCheckRequest
{
    /**
     * @var CheckId
     */
    private $id;

    /**
     * @var CheckName
     */
    private $name;

    /**
     * @var CheckUrl
     */
    private $url;

    /**
     * @var CheckStatus
     */
    private $status;


    public function __construct(CheckId $id, CheckName $name, CheckUrl $url, CheckStatus $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->status = $status;
    }

    public function id(): CheckId
    {
        return $this->id;
    }

    public function name(): CheckName
    {
        return $this->name;
    }

    public function url(): CheckUrl
    {
        return $this->url;
    }

    public function status(): CheckStatus
    {
        return $this->status;
    }
}
