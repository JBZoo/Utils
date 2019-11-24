<?php
/**
 * JBZoo Utils
 *
 * This file is part of the JBZoo CCK package.
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
     * @param string|int|DateTime $time
     * @param bool                $currentIsDefault
     * @return int
     */
    public static function toStamp($time = null, $currentIsDefault = true): int
    {
        if ($time instanceof DateTime) {
            return $time->format('U');
        }

        if (null !== $time) {
            $time = is_numeric($time) ? (int)$time : strtotime($time);
        }

        if (!$time) {
            $time = $currentIsDefault ? time() : 0;
        }

        return $time;
    }

    /**
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
     * Return a DateTimeZone object based on the current timezone.
     *
     * @param mixed $timezone
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
     * @param string $date
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function is($date): bool
    {
        $time = strtotime($date);
        return $time > 0;
    }

    /**
     * Convert time for sql format
     *
     * @param null|int $time
     * @return string
     */
    public static function sql($time = null): string
    {
        return self::factory($time)->format(self::SQL_FORMAT);
    }

    /**
     * @param string|int $date
     * @param string     $format
     * @return string
     */
    public static function human($date, $format = 'd M Y H:i'): string
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
}
