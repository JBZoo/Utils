<?php
/**
 * JBZoo Utils
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Utils
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/Utils
 * @author    Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Timer;

/**
 * Class TimerTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class TimerTest extends PHPUnit
{

    /**
     * @dataProvider secondsProvider
     * @param string $string
     * @param mixed  $seconds
     */
    public function testSecondsToTimeString($string, $seconds)
    {
        isSame($string, Timer::format($seconds));
    }

    /**
     * @dataProvider milliSecondsProvider
     * @param string $string
     * @param mixed  $seconds
     */
    public function testSecondsToTimeStringInMillisecond($string, $seconds)
    {
        isSame($string, Timer::formatMS($seconds));
    }

    public function testGetRequestTime()
    {
        isTrue(Timer::getRequestTime());
    }

    public function testTimeSinceStart()
    {
        isTrue(Timer::timeSinceStart());
    }

    /**
     * @return array
     */
    public function milliSecondsProvider()
    {
        return array(
            array('1 000 ms', 1),
            array('100 ms', 0.100),
            array('106 ms', 0.1056),
            array('10 ms', 0.01),
            array('15 ms', 0.015),
            array('0 ms', 0),
            array('1.0 ms', 0.001),
            array('9.9 ms', 0.0099),
            array('10.0 ms', 0.00999),
            array('0.6 ms', 0.00055),
            array('0.010 ms', 0.00001),
            array('0.001 ms', 0.000001),
            array('0 ms', 0.0000001),
        );
    }

    /**
     * @return array
     */
    public function secondsProvider()
    {
        return array(
            array('0 ms', 0),
            array('1 ms', .001),
            array('10 ms', .01),
            array('100 ms', .1),
            array('999 ms', .999),
            array('1 sec', .9999),
            array('1 sec', 1),
            array('2 secs', 2),
            array('59.9 secs', 59.9),
            array('59.99 secs', 59.99),
            array('59.99 secs', 59.999),
            array('1 min', 59.9999),
            array('59 secs', 59.001),
            array('59.01 secs', 59.01),
            array('1 min', 60),
            array('1.01 mins', 61),
            array('2 mins', 120),
            array('2.01 mins', 121),
            array('59.99 mins', 3599.9),
            array('59.99 mins', 3599.99),
            array('59.99 mins', 3599.999),
            array('1 hour', 3599.9999),
            array('59.98 mins', 3599.001),
            array('59.98 mins', 3599.01),
            array('1 hour', 3600),
            array('1 hour', 3601),
            array('1 hour', 3601.9),
            array('1 hour', 3601.99),
            array('1 hour', 3601.999),
            array('1 hour', 3601.9999),
            array('1.01 hours', 3659.9999),
            array('1.01 hours', 3659.001),
            array('1.01 hours', 3659.01),
            array('2 hours', 7199.9999),
        );
    }
}
