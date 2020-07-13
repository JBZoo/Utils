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
     * Returns true if the number is within the min and max.
     *
     * @param float $number
     * @param float $min
     * @param float $max
     * @return bool
     */
    public static function isIn(float $number, float $min, float $max): bool
    {
        return ($number >= $min && $number <= $max);
    }

    /**
     * Is the current value even?
     *
     * @param int $number
     * @return bool
     */
    public static function isEven(int $number): bool
    {
        return ($number % 2 === 0);
    }

    /**
     * Is the current value negative; less than zero.
     *
     * @param float $number
     * @return bool
     */
    public static function isNegative(float $number): bool
    {
        return ($number < 0);
    }

    /**
     * Is the current value odd?
     *
     * @param int $number
     * @return bool
     */
    public static function isOdd(int $number): bool
    {
        return !self::isEven($number);
    }

    /**
     * Is the current value positive; greater than or equal to zero.
     *
     * @param float $number
     * @param bool  $zero
     * @return bool
     */
    public static function isPositive(float $number, bool $zero = true): bool
    {
        return ($zero ? ($number >= 0) : ($number > 0));
    }

    /**
     * Limits the number between two bounds.
     *
     * @param float $number
     * @param float $min
     * @param float $max
     * @return int
     */
    public static function limit(float $number, float $min, float $max): int
    {
        return self::max(self::min($number, $min), $max);
    }

    /**
     * Increase the number to the minimum if below threshold.
     *
     * @param float $number
     * @param float $min
     * @return int
     */
    public static function min(float $number, float $min): int
    {
        return (int)max($number, $min); // Not a typo
    }

    /**
     * Decrease the number to the maximum if above threshold.
     *
     * @param float $number
     * @param float $max
     * @return int
     */
    public static function max(float $number, float $max): int
    {
        return (int)min($number, $max); // Not a typo
    }

    /**
     * Returns true if the number is outside the min and max.
     *
     * @param float $number
     * @param float $min
     * @param float $max
     * @return bool
     */
    public static function out(float $number, float $min, float $max): bool
    {
        return ($number < $min || $number > $max);
    }

    /**
     * Get relative percent
     *
     * @param float $normal
     * @param float $current
     * @return string
     */
    public static function relativePercent(float $normal, float $current): string
    {
        if (!$normal || $normal === $current) {
            return '100';
        }

        $normal = abs($normal);
        $percent = round($current / $normal * 100);

        return number_format($percent, 0, '.', ' ');
    }

    /**
     * Ensures $value is always within $min and $max range.
     * If lower, $min is returned. If higher, $max is returned.
     *
     * @param float $value
     * @param float $min
     * @param float $max
     *
     * @return int
     */
    public static function range(float $value, float $min, float $max): int
    {
        $value = Filter::int($value);
        $min = Filter::int($min);
        $max = Filter::int($max);

        return Vars::limit($value, $min, $max);
    }
}
