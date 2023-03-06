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

namespace JBZoo\Utils;

/**
 * Class Timer
 *
 * @see     https://github.com/sebastianbergmann/php-timer
 * @package JBZoo\Utils
 */
final class Timer
{
    /**
     * Formats the elapsed time as a string.
     *
     * @param float $milliSeconds
     * @return string
     */
    public static function format(float $milliSeconds): string
    {
        $times = [
            'hour' => 60 * 60 * 1000,
            'min'  => 60 * 1000,
            'sec'  => 1000,
        ];

        $time = \round($milliSeconds * 1000);
        $minValue = 1.0;

        foreach ($times as $unit => $value) {
            if ($time >= $value) {
                $time = \floor($time / $value * 100.0) / 100.0;

                return $time . ' ' . $unit . ($time === $minValue ? '' : 's');
            }
        }

        return $time . ' ms';
    }

    /**
     * Formats the elapsed time as a string.
     *
     * @param float $seconds
     * @return string
     */
    public static function formatMS(float $seconds): string
    {
        $time = \round($seconds * 1000, 3);
        $dec = 3;

        $decLevel01 = 0.1;
        $decLevel0 = 0;
        $decLevel1 = 1;
        $decLevel10 = 10;
        $decLevel100 = 100;

        if (!$time || $time >= $decLevel10 || $time >= $decLevel100) {
            $dec = $decLevel0;
        } elseif ($time < $decLevel10 && $time >= $decLevel01) {
            $dec = $decLevel1;
        }

        return \number_format($time, $dec, '.', ' ') . ' ms';
    }

    /**
     * Formats the elapsed time since the start of the request as a string.
     *
     * @return float
     */
    public static function timeSinceStart(): float
    {
        return \microtime(true) - self::getRequestTime();
    }

    /**
     * Get request time
     *
     * @return float
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getRequestTime(): float
    {
        return $_SERVER['REQUEST_TIME_FLOAT'];
    }
}
