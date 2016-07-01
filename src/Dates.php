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

use \DateTime;
use \DateTimeZone;

/**
 * Class Dates
 * @package JBZoo\Utils
 */
class Dates
{
    const MINUTE = 60;
    const HOUR   = 3600;
    const DAY    = 86400;
    const WEEK   = 604800;      // 7 days
    const MONTH  = 2592000;     // 30 days
    const YEAR   = 31536000;    // 365 days

    const SQL_FORMAT = 'Y-m-d H:i:s';
    const SQL_NULL   = '0000-00-00 00:00:00';

    /**
     * Convert to timestamp
     *
     * @param string|DateTime $time
     * @param bool            $currentIsDefault
     * @return int
     */
    public static function toStamp($time = null, $currentIsDefault = true)
    {
        if ($time instanceof DateTime) {
            return $time->format('U');
        }

        if (!empty($time)) {
            if (is_numeric($time)) {
                $time = (int)$time;
            } else {
                $time = strtotime($time);
            }
        }

        if (!$time) {
            if ($currentIsDefault) {
                $time = time();
            } else {
                $time = 0;
            }
        }

        return $time;
    }

    /**
     * @param mixed $time
     * @param null  $timeZone
     * @return DateTime
     */
    public static function factory($time = null, $timeZone = null)
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
     * @return \DateTimeZone
     */
    public static function timezone($timezone = null)
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
    public static function is($date)
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
    public static function sql($time = null)
    {
        return self::factory($time)->format(self::SQL_FORMAT);
    }

    /**
     * @param string|int $date
     * @param string     $format
     * @return string
     */
    public static function human($date, $format = 'd M Y H:i')
    {
        return self::factory($date)->format($format);
    }

    /**
     * Returns true if date passed is within this week.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isThisWeek($time)
    {
        return (self::factory($time)->format('W-Y') === self::factory()->format('W-Y'));
    }

    /**
     * Returns true if date passed is within this month.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isThisMonth($time)
    {
        return (self::factory($time)->format('m-Y') === self::factory()->format('m-Y'));
    }

    /**
     * Returns true if date passed is within this year.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isThisYear($time)
    {
        return (self::factory($time)->format('Y') === self::factory()->format('Y'));
    }

    /**
     * Returns true if date passed is tomorrow.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isTomorrow($time)
    {
        return (self::factory($time)->format('Y-m-d') === self::factory('tomorrow')->format('Y-m-d'));
    }

    /**
     * Returns true if date passed is today.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isToday($time)
    {
        return (self::factory($time)->format('Y-m-d') === self::factory()->format('Y-m-d'));
    }

    /**
     * Returns true if date passed was yesterday.
     *
     * @param string|int $time
     * @return bool
     */
    public static function isYesterday($time)
    {
        return (self::factory($time)->format('Y-m-d') === self::factory('yesterday')->format('Y-m-d'));
    }
}
