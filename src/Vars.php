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

/**
 * Class Filter
 *
 * @package JBZoo\Utils
 */
class Vars
{
    /**
     * Return true if the number is within the min and max.
     *
     * @param int|float $number
     * @param int|float $min
     * @param int|float $max
     * @return bool
     */
    public static function isIn($number, $min, $max): bool
    {
        return ($number >= $min && $number <= $max);
    }

    /**
     * Is the current value even?
     *
     * @param int $number
     * @return bool
     */
    public static function isEven($number): bool
    {
        return ($number % 2 === 0);
    }

    /**
     * Is the current value negative; less than zero.
     *
     * @param int $number
     * @return bool
     */
    public static function isNegative($number): bool
    {
        return ($number < 0);
    }

    /**
     * Is the current value odd?
     *
     * @param int $number
     * @return bool
     */
    public static function isOdd($number): bool
    {
        return !self::isEven($number);
    }

    /**
     * Is the current value positive; greater than or equal to zero.
     *
     * @param int  $number
     * @param bool $zero
     * @return bool
     */
    public static function isPositive($number, $zero = true): bool
    {
        return ($zero ? ($number >= 0) : ($number > 0));
    }

    /**
     * Limits the number between two bounds.
     *
     * @param int $number
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function limit($number, $min, $max): int
    {
        return self::max(self::min($number, $min), $max);
    }

    /**
     * Increase the number to the minimum if below threshold.
     *
     * @param int $number
     * @param int $min
     * @return int
     */
    public static function min($number, $min): int
    {
        if ($number < $min) {
            $number = $min;
        }
        return $number;
    }

    /**
     * Decrease the number to the maximum if above threshold.
     *
     * @param int $number
     * @param int $max
     * @return int
     */
    public static function max($number, $max): int
    {
        if ($number > $max) {
            $number = $max;
        }
        return $number;
    }

    /**
     * Return true if the number is outside the min and max.
     *
     * @param int $number
     * @param int $min
     * @param int $max
     * @return bool
     */
    public static function out($number, $min, $max): bool
    {
        return ($number < $min || $number > $max);
    }

    /**
     * Get relative percent
     *
     * @param float|int $normal
     * @param float|int $current
     * @return string
     */
    public static function relativePercent($normal, $current): string
    {
        $normal = (float)$normal;
        $current = (float)$current;

        if (!$normal || $normal === $current) {
            return '100';
        }

        $normal = abs($normal);
        $percent = round($current / $normal * 100);

        return number_format($percent, 0, '.', ' ');
    }
}
