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

namespace JBZoo\Utils;

/**
 * Class Timer
 * @see     https://github.com/sebastianbergmann/php-timer
 * @package JBZoo\Utils
 */
class Timer
{
    /**
     * @var float
     */
    public static $requestTime;

    /**
     * @var array
     */
    private static $_times = array(
        'hour' => 3600000,
        'min'  => 60000,
        'sec'  => 1000,
    );

    /**
     * Formats the elapsed time as a string.
     *
     * @param  float $time
     * @return string
     */
    public static function format($time)
    {
        $time = round($time * 1000);
        foreach (self::$_times as $unit => $value) {
            if ($time >= $value) {
                $time = floor($time / $value * 100.0) / 100.0;
                return $time . ' ' . $unit . ($time == 1 ? '' : 's');
            }
        }

        return $time . ' ms';
    }

    /**
     * Formats the elapsed time as a string.
     *
     * @param  float $time
     * @return string
     */
    public static function formatMS($time)
    {
        $time = round($time * 1000, 3);
        $dec  = 3;

        if (!$time || $time >= 10 || $time >= 100) {
            $dec = 0;
        } elseif ($time < 10 && $time >= 0.1) {
            $dec = 1;
        } elseif ($time <= 0.01) {
            $dec = 3;
        }

        return number_format($time, $dec, '.', ' ') . ' ms';
    }

    /**
     * Formats the elapsed time since the start of the request as a string.
     *
     * @return float
     */
    public static function timeSinceStart()
    {
        return microtime(true) - self::getRequestTime();
    }

    /**
     * Get request time
     *
     * @return float
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getRequestTime()
    {
        return $_SERVER['REQUEST_TIME_FLOAT'];
    }
}
