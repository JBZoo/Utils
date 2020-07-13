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
 * Class Math
 * @see     Class based on Statistics.php from phpbench/phpbench
 * @package JBZoo\Utils
 */
class Stats
{
    /**
     * Returns the standard deviation of a given population.
     *
     * @param array $values
     * @param bool  $sample
     *
     * @return float
     */
    public static function stdDev(array $values, bool $sample = false): float
    {
        $variance = self::variance($values, $sample);
        return \sqrt($variance);
    }

    /**
     * Returns the variance for a given population.
     *
     * @param array $values
     * @param bool  $sample
     *
     * @return float
     */
    public static function variance(array $values, bool $sample = false): float
    {
        $average = self::mean($values);
        $sum = 0;

        foreach ($values as $value) {
            $diff = ($value - $average) ** 2;
            $sum += $diff;
        }

        if (count($values) === 0) {
            return 0;
        }

        return $sum / (count($values) - ($sample ? 1 : 0));
    }

    /**
     * Returns the mean (average) value of the given values.
     *
     * @param array|null $values
     * @return float
     */
    public static function mean(?array $values): float
    {
        if (empty($values)) {
            return 0;
        }

        $sum = array_sum($values);

        if (!$sum) {
            return 0;
        }

        $count = count($values);

        return $sum / $count;
    }

    /**
     * Returns an array populated with $num numbers from $min to $max.
     *
     * @param float $min
     * @param float $max
     * @param int   $num
     * @param bool  $endpoint
     *
     * @return float[]
     */
    public static function linSpace(float $min, float $max, int $num = 50, bool $endpoint = true): array
    {
        $range = $max - $min;

        if ($max === $min) {
            throw new Exception("Min and max cannot be the same number: {$max}");
        }

        $unit = $range / ($endpoint ? $num - 1 : $num);
        $space = [];

        for ($value = $min; $value <= $max; $value += $unit) {
            $space[] = $value;
        }

        if (!$endpoint) {
            array_pop($space);
        }

        return $space;
    }

    /**
     * Generate a histogram.
     *
     * Note this is not a great function, and should not be relied upon
     * for serious use.
     *
     * For a better implementation copy:
     *   http://docs.scipy.org/doc/numpy-1.10.1/reference/generated/numpy.histogram.html
     *
     * @param array      $values
     * @param int        $steps
     * @param float|null $lowerBound
     * @param float|null $upperBound
     *
     * @return array
     */
    public static function histogram(
        array $values,
        int $steps = 10,
        ?float $lowerBound = null,
        ?float $upperBound = null
    ): array {
        $min = $lowerBound ?? min($values);
        $max = $upperBound ?? max($values);

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
     * Render human readable string of average value and system error
     *
     * @param array $values
     * @param int   $rounding
     * @return string
     */
    public static function renderAverage(array $values, int $rounding = 3): string
    {
        $avg = number_format(self::mean($values), $rounding);
        $stdDev = number_format(self::stdDev($values), $rounding);

        return "{$avg}±{$stdDev}";
    }
}
