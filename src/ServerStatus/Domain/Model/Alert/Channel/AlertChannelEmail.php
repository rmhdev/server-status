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

namespace ServerStatus\Domain\Model\Alert\Channel;

class AlertChannelEmail implements AlertChannel
{
    const NAME = "email";

    /**
     * @var string
     */
    private $destinationRaw;


    public function __construct(string $destinationRaw)
    {
        $this->destinationRaw = $destinationRaw;
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return self::NAME;
    }

    /**
     * @inheritdoc
     */
    public function destinationRaw(): string
    {
        return $this->destinationRaw;
    }

    /**
     * @inheritdoc
     */
    public function destinations(): array
    {
        return explode(",", preg_replace('/\s+/', '', $this->destinationRaw));
    }
}
