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

final class Dates
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
     * Convert to timestamp.
     */
    public static function toStamp(null|\DateTime|int|string $time = null, bool $currentIsDefault = true): int
    {
        if ($time instanceof \DateTime) {
            return (int)$time->format('U');
        }

        if ($time !== null) {
            $time = \is_numeric($time) ? (int)$time : (int)\strtotime($time);
        }

        if ($time === 0 || $time === null) {
            $time = $currentIsDefault ? \time() : 0;
        }

        return $time;
    }

    /**
     * Build PHP \DateTime object from mixed input.
     */
    public static function factory(mixed $time = null, null|\DateTimeZone|string $timeZone = null): \DateTime
    {
        $timeZone = self::timezone($timeZone);

        if ($time instanceof \DateTime) {
            return $time->setTimezone($timeZone);
        }

        $dateTime = new \DateTime('@' . self::toStamp($time));
        $dateTime->setTimezone($timeZone);

        return $dateTime;
    }

    /**
     * Returns a DateTimeZone object based on the current timezone.
     */
    public static function timezone(null|\DateTimeZone|string $timezone = null): \DateTimeZone
    {
        if ($timezone instanceof \DateTimeZone) {
            return $timezone;
        }

        $timezone = ($timezone === '' || $timezone === null) ? \date_default_timezone_get() : $timezone;

        return new \DateTimeZone($timezone);
    }

    /**
     * Check if string is date.
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function is(?string $date): bool
    {
        $time = \strtotime((string)$date);

        return $time > 0;
    }

    /**
     * Convert time for sql format.
     */
    public static function sql(null|int|string $time = null): string
    {
        return self::factory($time)->format(self::SQL_FORMAT);
    }

    /**
     * Convert date string ot unix timestamp to human-readable date format.
     */
    public static function human(int|string $date, string $format = 'd M Y H:i'): string
    {
        return self::factory($date)->format($format);
    }

    /**
     * Returns true if date passed is within this week.
     */
    public static function isThisWeek(int|string $time): bool
    {
        return self::factory($time)->format('W-Y') === self::factory()->format('W-Y');
    }

    /**
     * Returns true if date passed is within this month.
     */
    public static function isThisMonth(int|string $time): bool
    {
        return self::factory($time)->format('m-Y') === self::factory()->format('m-Y');
    }

    /**
     * Returns true if date passed is within this year.
     */
    public static function isThisYear(int|string $time): bool
    {
        return self::factory($time)->format('Y') === self::factory()->format('Y');
    }

    /**
     * Returns true if date passed is tomorrow.
     */
    public static function isTomorrow(int|string $time): bool
    {
        return self::factory($time)->format('Y-m-d') === self::factory('tomorrow')->format('Y-m-d');
    }

    /**
     * Returns true if date passed is today.
     */
    public static function isToday(int|string $time): bool
    {
        return self::factory($time)->format('Y-m-d') === self::factory()->format('Y-m-d');
    }

    /**
     * Returns true if date passed was yesterday.
     */
    public static function isYesterday(int|string $time): bool
    {
        return self::factory($time)->format('Y-m-d') === self::factory('yesterday')->format('Y-m-d');
    }

    /**
     * Convert seconds to human-readable format "H:i:s".
     */
    public static function formatTime(float $seconds, int $minValuableSeconds = 2): string
    {
        if ($seconds < $minValuableSeconds) {
            return \number_format($seconds, 3) . ' sec';
        }

        return \gmdate('H:i:s', (int)\round($seconds, 0));
    }
}
