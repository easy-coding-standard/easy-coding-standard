<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-timer.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\Timer;

use function array_pop;
use function hrtime;
final class Timer
{
    /**
     * @psalm-var list<float>
     */
    private $startTimes = [];
    public function start() : void
    {
        $this->startTimes[] = (float) \hrtime(\true);
    }
    /**
     * @throws NoActiveTimerException
     */
    public function stop() : \ECSPrefix20210804\SebastianBergmann\Timer\Duration
    {
        if (empty($this->startTimes)) {
            throw new \ECSPrefix20210804\SebastianBergmann\Timer\NoActiveTimerException('Timer::start() has to be called before Timer::stop()');
        }
        return \ECSPrefix20210804\SebastianBergmann\Timer\Duration::fromNanoseconds((float) \hrtime(\true) - \array_pop($this->startTimes));
    }
}
