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

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        $destinations = $this->destinations();
        if (2 >= sizeof($destinations)) {
            return sprintf('email to %s', implode(" and ", $this->destinations()));
        }
        $show = array_slice($destinations, 0, 2);
        $countExtra =sizeof(array_slice($destinations, 2));

        return sprintf(
            'email to %s and %s more address%s',
            implode(", ", $show),
            $countExtra,
            $countExtra > 1 ? 'es' : ''
        );
    }
}
