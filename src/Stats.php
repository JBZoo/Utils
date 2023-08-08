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
 * @see https://github.com/phpbench/phpbench/blob/master/lib/Math/Statistics.php
 */
final class Stats
{
    /**
     * Returns the standard deviation of a given population.
     */
    public static function stdDev(array $values, bool $sample = false): float
    {
        $variance = self::variance($values, $sample);

        return \sqrt($variance);
    }

    /**
     * Returns the variance for a given population.
     */
    public static function variance(array $values, bool $sample = false): float
    {
        $average = self::mean($values);
        $sum     = 0;

        foreach ($values as $value) {
            $diff = ($value - $average) ** 2;
            $sum += $diff;
        }

        if (\count($values) === 0) {
            return 0;
        }

        return $sum / (\count($values) - ($sample ? 1 : 0));
    }

    /**
     * Returns the mean (average) value of the given values.
     */
    public static function mean(?array $values): float
    {
        if ($values === null || \count($values) === 0) {
            return 0;
        }

        $sum = \array_sum($values);

        if ($sum === 0) {
            return 0;
        }

        $count = \count($values);

        return $sum / $count;
    }

    /**
     * Returns an array populated with $num numbers from $min to $max.
     * @return float[]
     */
    public static function linSpace(float $min, float $max, int $num = 50, bool $endpoint = true): array
    {
        $range = $max - $min;

        if ($max === $min) {
            throw new Exception("Min and max cannot be the same number: {$max}");
        }

        $unit  = $range / ($endpoint ? $num - 1 : $num);
        $space = [];

        for ($value = $min; $value <= $max; $value += $unit) {
            $space[] = $value;
        }

        if (!$endpoint) {
            \array_pop($space);
        }

        return $space;
    }

    /**
     * Generate a histogram. Note this is not a great function, and should not be relied upon for serious use.
     * @see http://docs.scipy.org/doc/numpy-1.10.1/reference/generated/numpy.histogram.html
     * @param float[] $values
     */
    public static function histogram(
        array $values,
        int $steps = 10,
        ?float $lowerBound = null,
        ?float $upperBound = null,
    ): array {
        if (\count($values) === 0) {
            throw new Exception('Empty array of values is given');
        }

        $min = $lowerBound ?? \min($values);
        $max = $upperBound ?? \max($values);

        $range = $max - $min;

        $step = $range / $steps;
        $steps++; // add one extra step to catch the max value

        $histogram = [];

        $floor = $min;

        for ($i = 0; $i < $steps; $i++) {
            $ceil = $floor + $step;

            if (!isset($histogram[(string)$floor])) {
                $histogram[(string)$floor] = 0;
            }

            foreach ($values as $value) {
                if ($value >= $floor && $value < $ceil) {
                    $histogram[(string)$floor]++;
                }
            }

            $floor += $step;
        }

        return $histogram;
    }

    /**
     * Render human readable string of average value and system error.
     */
    public static function renderAverage(array $values, int $rounding = 3): string
    {
        $avg    = \number_format(self::mean($values), $rounding);
        $stdDev = \number_format(self::stdDev($values), $rounding);

        return "{$avg}±{$stdDev}";
    }

    /**
     * Calculate the percentile of a given population.
     * @param float[]|int[] $data
     */
    public static function percentile(array $data, float|int $percentile = 95): ?float
    {
        $count = \count($data);
        if ($count === 0 || $percentile <= 0) {
            return null;
        }

        $validPercentile = $percentile * 0.01;
        $allIndex        = ($count - 1) * $validPercentile;
        $intValue        = (int)$allIndex;
        $floatValue      = $allIndex - $intValue;

        \sort($data);

        if (!\is_float($floatValue)) {
            $result = $data[$intValue];
        } elseif ($intValue + 1 < $count) {
            $result = $data[$intValue] + ($data[$intValue + 1] - $data[$intValue]) * $floatValue;
        } else {
            $result = $data[$intValue];
        }

        return $result;
    }

    /**
     * Calculate the median of a given population.
     * @param float[]|int[] $data
     */
    public static function median(array $data): ?float
    {
        return self::percentile($data, 50.0);
    }
}
