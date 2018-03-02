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

namespace ServerStatus\Infrastructure\Ui\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

abstract class AbstractCommand extends Command
{
    /**
     * @var Stopwatch
     */
    private $stopWatch;

    /**
     * Taken from sebastianbergmann/php-timer package
     *
     * @param float $time
     * @return string
     */
    protected function formatTime(float $time)
    {
        $times = array(
            'hour'   => 3600000,
            'minute' => 60000,
            'second' => 1000
        );
        $ms = $time;
        foreach ($times as $unit => $value) {
            if ($ms >= $value) {
                $time = floor($ms / $value * 100.0) / 100.0;

                return $time . ' ' . ($time == 1 ? $unit : $unit . 's');
            }
        }

        return $ms . ' ms';
    }

    protected function formatMemory(int $bytes = 0, $precision = 2)
    {
        return sprintf("%4.2fMB", round($bytes / 2**20, $precision));
    }

    protected function startWatch(): bool
    {
        if (!is_null($this->stopWatch)) {
            return false;
        }

        $this->stopWatch = new Stopwatch(true);
        $this->stopWatch->start($this->watchName());

        return true;
    }

    protected function watchName(): string
    {
        return "my-watch";
    }

    protected function stopWatch(): StopwatchEvent
    {
        if (is_null($this->stopWatch)) {
            $stopWatch = new Stopwatch(true);
            $stopWatch->start("fake");

            return $stopWatch->stop("fake");
        }

        return $this->stopWatch->stop($this->watchName());
    }

    protected function writeCompletedMessage(OutputInterface $output, StopwatchEvent $event)
    {
        $output->writeln("Completed");
        $output->writeln(sprintf(
            "Time: %s, Memory: %s",
            $this->formatTime($event->getDuration()),
            $this->formatMemory($event->getMemory())
        ));
    }
}
