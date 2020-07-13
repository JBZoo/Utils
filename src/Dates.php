<?php

/**
 * JBZoo Toolbox - Utils
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Utils
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Utils
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\Utils;

use DateTime;
use DateTimeZone;

/**
 * Class Dates
 *
 * @package JBZoo\Utils
 */
class Dates
{
    public const MINUTE = 60;
    public const HOUR   = 3600;
    public const DAY    = 86400;
    public const WEEK   = 604800;      // 7 days
    public const MONTH  = 2592000;     // 30 days
    public const YEAR   = 31536000;    // 365 days

    public const SQL_FORMAT = 'Y-m-d H:i:s';
    public const SQL_NULL   = '0000-00-00 00:00:00';

    /**
     * Convert to timestamp
     *
     * @param string|int|DateTime|null $time
     * @param bool                     $currentIsDefault
     * @return int
     */
    public static function toStamp($time = null, bool $currentIsDefault = true): int
    {
        if ($time instanceof DateTime) {
            return (int)$time->format('U');
        }

        if (null !== $time) {
            $time = is_numeric($time) ? (int)$time : (int)strtotime($time);
        }

        if (!$time) {
            $time = $currentIsDefault ? time() : 0;
        }

        return $time;
    }

    /**
     * Build PHP \DateTime object from mixed input
     *
     * @param mixed $time
     * @param null  $timeZone
     * @return DateTime
     */
    public static function factory($time = null, $timeZone = null): DateTime
    {
        $timeZone = self::timezone($timeZone);

        if ($time instanceof DateTime) {
            return $time->setTimezone($timeZone);
        }

        $dateTime = new DateTime('@' . self::toStamp($time));
        $dateTime->setTimezone($timeZone);

        return $dateTime;
    }

    /**
     * Returns a DateTimeZone object based on the current timezone.
     *
     * @param DateTimeZone|string|null $timezone
     * @return DateTimeZone
     */
    public static function timezone($timezone = null): DateTimeZone
    {
        if ($timezone instanceof DateTimeZone) {
            return $timezone;
        }

        $timezone = $timezone ?: date_default_timezone_get();

        return new DateTimeZone($timezone);
    }

    /**
     * Check if string is date
     *
     * @param string|null $date
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function is(?string $date): bool
    {
        $time = strtotime((string)$date);
        return $time > 10000;
    }

    /**
     * Convert time for sql format
     *
     * @param string|int|null $time
     * @return string
     */
    public static function sql($time = null): string
    {
        return self::factory($time)->format(self::SQL_FORMAT);
    }

    /**
     * Convert date string ot unix timestamp to human readable date format
     *
     * @param string|int $date
     * @param string     $format
     * @return string
     */
    public static function human($date, string $format = 'd M Y H:i'): string
    {
        return self::factory($date)->format($format);
    }

    /**
     * Returns true if date passed is within this week.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isThisWeek($time): bool
    {
        return (self::factory($time)->format('W-Y') === self::factory()->format('W-Y'));
    }

    /**
     * Returns true if date passed is within this month.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isThisMonth($time): bool
    {
        return (self::factory($time)->format('m-Y') === self::factory()->format('m-Y'));
    }

    /**
     * Returns true if date passed is within this year.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isThisYear($time): bool
    {
        return (self::factory($time)->format('Y') === self::factory()->format('Y'));
    }

    /**
     * Returns true if date passed is tomorrow.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isTomorrow($time): bool
    {
        return (self::factory($time)->format('Y-m-d') === self::factory('tomorrow')->format('Y-m-d'));
    }

    /**
     * Returns true if date passed is today.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isToday($time): bool
    {
        return (self::factory($time)->format('Y-m-d') === self::factory()->format('Y-m-d'));
    }

    /**
     * Returns true if date passed was yesterday.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isYesterday($time): bool
    {
        return (self::factory($time)->format('Y-m-d') === self::factory('yesterday')->format('Y-m-d'));
    }

    /**
     * Convert seconds to human readable format "H:i:s"
     *
     * @param float $seconds
     * @return string
     */
    public static function formatTime(float $seconds): string
    {
        if ($seconds < 2) {
            return number_format($seconds, 3) . ' sec';
        }

        return (string)gmdate('H:i:s', (int)round($seconds, 0));
    }
}
