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
 */

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Stats;

/**
 * Class StatsTest
 *
 * @package JBZoo\PHPUnit
 */
class StatsTest extends PHPUnit
{
    public function testMean()
    {
        isSame(0.0, Stats::mean([]));
        isSame(0.0, Stats::mean(null));
        isSame(0.0, Stats::mean(['0', '']));

        isSame(1.0, Stats::mean([1]));
        isSame(1.0, Stats::mean([1, 1]));
        isSame(2.0, Stats::mean([1, 3]));
    }

    public function testStdDev()
    {
        isSame(0.0, Stats::stdDev([]));
        isSame(0.0, Stats::stdDev([1]));
        isSame(0.5, Stats::stdDev([1, 2]));
        isSame(0.5, Stats::stdDev([1, 2, 1, 2]));

        isSame(0.0, Stats::stdDev([], true));
        isSame(1.0, Stats::stdDev([1, 3, 2], true));
    }

    public function testLinSpace()
    {
        isSame([0.0, 5.0, 10.0, 15.0, 20.0], Stats::linSpace(0, 20, 5));
        isSame([0.0, 4.0, 8.0, 12.0, 16.0], Stats::linSpace(0, 20, 5, false));
    }

    public function testHistogram()
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
            '2'   => 0
        ], Stats::histogram([1, 2, 1]));

        isSame([
            '1'   => 2,
            '1.2' => 0,
            '1.4' => 0,
            '1.6' => 0,
            '1.8' => 0,
            '2'   => 1
        ], Stats::histogram([1, 2, 1], 5, 1, 2));

        isSame(['1' => 0], Stats::histogram([1, 2, 1], 5, 1, 1));
        isSame(['2' => 0], Stats::histogram([1, 2, 1], 5, 2, 2));
    }

    public function testRenderAverage()
    {
        isSame('1.500±0.500', Stats::renderAverage([1, 2, 1, 2]));
        isSame('1.5±0.5', Stats::renderAverage([1, 2, 1, 2], 1));
        isSame('1.50±0.50', Stats::renderAverage([1, 2, 1, 2], 2));
        isSame('2±1', Stats::renderAverage([1, 2, 1, 2], 0));
        isSame('2±1', Stats::renderAverage([1, 2, 1, 2], -1));
    }
}
