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

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Stats;

class StatsTest extends PHPUnit
{
    public function testMean(): void
    {
        isSame(0.0, Stats::mean([]));
        isSame(0.0, Stats::mean(null));
        isSame(0.0, Stats::mean(['0', '']));

        isSame(1.0, Stats::mean([1]));
        isSame(1.0, Stats::mean([1, 1]));
        isSame(2.0, Stats::mean([1, 3]));
        isSame(2.0, Stats::mean(['1', 3]));
        isSame(2.25, Stats::mean(['1.5', 3]));
    }

    public function testStdDev(): void
    {
        isSame(0.0, Stats::stdDev([]));
        isSame(0.0, Stats::stdDev([1]));
        isSame(0.5, Stats::stdDev([1, 2]));
        isSame(0.5, Stats::stdDev([1, 2, 1, 2]));

        isSame(0.0, Stats::stdDev([], true));
        isSame(1.0, Stats::stdDev([1, 3, 2], true));
    }

    public function testLinSpace(): void
    {
        isSame([0.0, 5.0, 10.0, 15.0, 20.0], Stats::linSpace(0, 20, 5));
        isSame([0.0, 4.0, 8.0, 12.0, 16.0], Stats::linSpace(0, 20, 5, false));
    }

    public function testHistogram(): void
    {
        isSame([1 => 0], Stats::histogram([1]));
        isSame([1 => 0], Stats::histogram([1, 1, 1]));
        isSame([
            '1'   => 2,
            '1.1' => 0,
            '1.2' => 0,
            '1.3' => 0,
            '1.4' => 0,
            '1.5' => 0,
            '1.6' => 0,
            '1.7' => 0,
            '1.8' => 0,
            '1.9' => 1,
            '2'   => 0,
        ], Stats::histogram([1, 2, 1]));

        isSame([
            '1'   => 2,
            '1.2' => 0,
            '1.4' => 0,
            '1.6' => 0,
            '1.8' => 0,
            '2'   => 1,
        ], Stats::histogram([1, 2, 1], 5, 1, 2));

        isSame(['1' => 0], Stats::histogram([1, 2, 1], 5, 1, 1));
        isSame(['2' => 0], Stats::histogram([1, 2, 1], 5, 2, 2));
    }

    public function testRenderAverage(): void
    {
        isSame('1.500±0.500', Stats::renderAverage([1, 2, 1, 2]));
        isSame('1.5±0.5', Stats::renderAverage([1, 2, 1, 2], 1));
        isSame('1.50±0.50', Stats::renderAverage([1, 2, 1, 2], 2));
        isSame('2±1', Stats::renderAverage([1, 2, 1, 2], 0));
        isSame('2±1', Stats::renderAverage([1, 2, 1, 2], -1));
    }

    public function testPercentile(): void
    {
        isSame(null, Stats::percentile([], 90));
        isSame(null, Stats::percentile([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 0));

        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        isSame(1.0, Stats::percentile([1], 90));
        isSame(1.09, Stats::percentile($data, 1));
        isSame(1.9, Stats::percentile($data, 10));
        isSame(2.8, Stats::percentile($data, 20));
        isSame(3.6999999999999997, Stats::percentile($data, 30));
        isSame(4.6, Stats::percentile($data, 40));
        isSame(5.5, Stats::percentile($data, 50));
        isSame(6.3999999999999995, Stats::percentile($data, 60));
        isSame(7.300000000000001, Stats::percentile($data, 70));
        isSame(8.2, Stats::percentile($data, 80));
        isSame(9.1, Stats::percentile($data, 90));
        isSame(9.91, Stats::percentile($data, 99));
        isSame(10.0, Stats::percentile($data, 100));
        isSame(5.5, Stats::percentile(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'], 50));
        isSame(5.5, Stats::percentile(['1.0', '2.0', '3.0', '4.0', '5.0', '6.0', '7.0', '8.0', '9.0', '10.0'], 50));
    }

    public function testMedian(): void
    {
        isSame(null, Stats::median([]));
        isSame(1.0, Stats::median([1]));
        isSame(1.5, Stats::median([1, 2]));
        isSame(5.5, Stats::median([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]));
    }
}
