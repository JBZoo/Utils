<?php

/**
 * JBZoo Toolbox - Utils.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Utils
 */

declare(strict_types=1);

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Timer;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class TimerTest extends PHPUnit
{
    /**
     * @dataProvider provideSecondsToTimeStringCases
     * @param string $string
     * @param mixed  $seconds
     */
    public function testSecondsToTimeString($string, $seconds): void
    {
        isSame($string, Timer::format($seconds));
    }

    /**
     * @dataProvider provideSecondsToTimeStringInMillisecondCases
     * @param string $string
     * @param mixed  $seconds
     */
    public function testSecondsToTimeStringInMillisecond($string, $seconds): void
    {
        isSame($string, Timer::formatMS($seconds));
    }

    public function testGetRequestTime(): void
    {
        isTrue(Timer::getRequestTime() > 0);
    }

    public function testTimeSinceStart(): void
    {
        isTrue(Timer::timeSinceStart() > 0);
    }

    public function provideSecondsToTimeStringInMillisecondCases(): iterable
    {
        return [
            ['1 000 ms', 1],
            ['100 ms', 0.100],
            ['106 ms', 0.1056],
            ['10 ms', 0.01],
            ['15 ms', 0.015],
            ['0 ms', 0],
            ['1.0 ms', 0.001],
            ['9.9 ms', 0.0099],
            ['10.0 ms', 0.00999],
            ['0.6 ms', 0.00055],
            ['0.010 ms', 0.00001],
            ['0.001 ms', 0.000001],
            ['0 ms', 0.0000001],
        ];
    }

    public function provideSecondsToTimeStringCases(): iterable
    {
        return [
            ['0 ms', 0],
            ['1 ms', .001],
            ['10 ms', .01],
            ['100 ms', .1],
            ['999 ms', .999],
            ['1 sec', .9999],
            ['1 sec', 1],
            ['2 secs', 2],
            ['59.9 secs', 59.9],
            ['59.99 secs', 59.99],
            ['59.99 secs', 59.999],
            ['1 min', 59.9999],
            ['59 secs', 59.001],
            ['59.01 secs', 59.01],
            ['1 min', 60],
            ['1.01 mins', 61],
            ['2 mins', 120],
            ['2.01 mins', 121],
            ['59.99 mins', 3599.9],
            ['59.99 mins', 3599.99],
            ['59.99 mins', 3599.999],
            ['1 hour', 3599.9999],
            ['59.98 mins', 3599.001],
            ['59.98 mins', 3599.01],
            ['1 hour', 3600],
            ['1 hour', 3601],
            ['1 hour', 3601.9],
            ['1 hour', 3601.99],
            ['1 hour', 3601.999],
            ['1 hour', 3601.9999],
            ['1.01 hours', 3659.9999],
            ['1.01 hours', 3659.001],
            ['1.01 hours', 3659.01],
            ['2 hours', 7199.9999],
        ];
    }
}
