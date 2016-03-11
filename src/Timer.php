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
        $ms = round($time * 1000);

        foreach (self::$_times as $unit => $value) {
            if ($ms >= $value) {
                $time = floor($ms / $value * 100.0) / 100.0;
                return $time . ' ' . $unit . ($time == 1 ? '' : 's');
            }
        }

        return $ms . ' ms';
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
