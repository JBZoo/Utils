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

final class Vars
{
    /**
     * Returns true if the number is within the min and max.
     */
    public static function isIn(float $number, float $min, float $max): bool
    {
        return $number >= $min && $number <= $max;
    }

    /**
     * Is the current value even?
     */
    public static function isEven(int $number): bool
    {
        return $number % 2 === 0;
    }

    /**
     * Is the current value negative; less than zero.
     */
    public static function isNegative(float $number): bool
    {
        return $number < 0;
    }

    /**
     * Is the current value odd?
     */
    public static function isOdd(int $number): bool
    {
        return !self::isEven($number);
    }

    /**
     * Is the current value positive; greater than or equal to zero.
     */
    public static function isPositive(float $number, bool $zero = true): bool
    {
        return $zero ? ($number >= 0) : ($number > 0);
    }

    /**
     * Limits the number between two bounds.
     */
    public static function limit(float $number, float $min, float $max): int
    {
        return self::max(self::min($number, $min), $max);
    }

    /**
     * Increase the number to the minimum if below threshold.
     */
    public static function min(float $number, float $min): int
    {
        return (int)\max($number, $min); // Not a typo
    }

    /**
     * Decrease the number to the maximum if above threshold.
     */
    public static function max(float $number, float $max): int
    {
        return (int)\min($number, $max); // Not a typo
    }

    /**
     * Returns true if the number is outside the min and max.
     */
    public static function out(float $number, float $min, float $max): bool
    {
        return $number < $min || $number > $max;
    }

    /**
     * Get relative percent.
     */
    public static function relativePercent(float $normal, float $current): string
    {
        if ($normal === 0.0 || $normal === $current) {
            return '100';
        }

        $normal  = \abs($normal);
        $percent = \round($current / $normal * 100);

        return \number_format($percent, 0, '.', ' ');
    }

    /**
     * Ensures $value is always within $min and $max range.
     * If lower, $min is returned. If higher, $max is returned.
     */
    public static function range(float $value, float $min, float $max): int
    {
        $value = Filter::int($value);
        $min   = Filter::int($min);
        $max   = Filter::int($max);

        return self::limit($value, $min, $max);
    }
}
