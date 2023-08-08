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

        $data = [72, 57, 66, 92, 32, 17, 146];
        isSame(68.857142857, Stats::mean($data));
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
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        isSame('5.500±2.872', Stats::renderAverage($data));
        isSame('5.5±2.9', Stats::renderAverage($data, 1));
        isSame('5.50±2.87', Stats::renderAverage($data, 2));
        isSame('6±3', Stats::renderAverage($data, 0));
        isSame('6±3', Stats::renderAverage($data, -1));

        $data = [72, 57, 66, 92, 32, 17, 146];
        isSame('68.857±39.084', Stats::renderAverage($data));
        isSame('68.9±39.1', Stats::renderAverage($data, 1));
        isSame('68.86±39.08', Stats::renderAverage($data, 2));
        isSame('69±39', Stats::renderAverage($data, 0));
        isSame('69±39', Stats::renderAverage($data, -1));
    }

    public function testRenderMedian(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        isSame('5.500±2.872', Stats::renderMedian($data));
        isSame('5.5±2.9', Stats::renderMedian($data, 1));
        isSame('5.50±2.87', Stats::renderMedian($data, 2));
        isSame('6±3', Stats::renderMedian($data, 0));
        isSame('6±3', Stats::renderMedian($data, -1));

        $data = [72, 57, 66, 92, 32, 17, 146];
        isSame('66.000±39.084', Stats::renderMedian($data));
        isSame('66.0±39.1', Stats::renderMedian($data, 1));
        isSame('66.00±39.08', Stats::renderMedian($data, 2));
        isSame('66±39', Stats::renderMedian($data, 0));
        isSame('66±39', Stats::renderMedian($data, -1));
    }

    public function testPercentile(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        isSame(1.0, Stats::percentile($data, 0));
        isSame(1.09, Stats::percentile($data, 1));
        isSame(1.9, Stats::percentile($data, 10));
        isSame(2.8, Stats::percentile($data, 20));
        isSame(3.7, Stats::percentile($data, 30));
        isSame(4.6, Stats::percentile($data, 40));
        isSame(5.5, Stats::percentile($data, 50));
        isSame(6.4, Stats::percentile($data, 60));
        isSame(7.3, Stats::percentile($data, 70));
        isSame(8.2, Stats::percentile($data, 80));
        isSame(9.1, Stats::percentile($data, 90));
        isSame(9.55, Stats::percentile($data));
        isSame(9.91, Stats::percentile($data, 99));
        isSame(9.9991, Stats::percentile($data, 99.99));
        isSame(10.0, Stats::percentile($data, 100));

        $data = [72, 57, 66, 92, 32, 17, 146];
        isSame(17.0, Stats::percentile($data, 0));
        isSame(17.9, Stats::percentile($data, 1));
        isSame(26.0, Stats::percentile($data, 10));
        isSame(37.0, Stats::percentile($data, 20));
        isSame(52.0, Stats::percentile($data, 30));
        isSame(60.6, Stats::percentile($data, 40));
        isSame(66.0, Stats::percentile($data, 50));
        isSame(69.6, Stats::percentile($data, 60));
        isSame(76.0, Stats::percentile($data, 70));
        isSame(88.0, Stats::percentile($data, 80));
        isSame(113.6, Stats::percentile($data, 90));
        isSame(129.8, Stats::percentile($data));
        isSame(142.76, Stats::percentile($data, 99));
        isSame(145.9676, Stats::percentile($data, 99.99));
        isSame(146.0, Stats::percentile($data, 100));

        isSame(0.0, Stats::percentile([], 0));
        isSame(0.0, Stats::percentile([], 90));
        isSame(0.0, Stats::percentile([0], 0));
        isSame(0.0, Stats::percentile([0], 90));
        isSame(1.0, Stats::percentile([1], 90));

        isSame(0.0, Stats::percentile(['qwerty'], 50));
        isSame(5.5, Stats::percentile(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'], 50));
        isSame(5.5, Stats::percentile(['1.0', '2.0', '3.0', '4.0', '5.0', '6.0', '7.0', '8.0', '9.0', '10.0'], 50));
        isSame(
            5.5,
            Stats::percentile([
                11 => '1.0',
                12 => '2.0',
                13 => '3.0',
                14 => '4.0',
                15 => '5.0',
                16 => '6.0',
                17 => '7.0',
                18 => '8.0',
                19 => '9.0',
                20 => '10.0',
            ], 50),
        );
    }

    public function testPercentileWithInvalidPercent1(): void
    {
        $this->expectException(\JBZoo\Utils\Exception::class);
        $this->expectExceptionMessage('Percentile should be between 0 and 100, 146 given');
        Stats::percentile([1, 2, 3], 146);
    }

    public function testPercentileWithInvalidPercent2(): void
    {
        $this->expectException(\JBZoo\Utils\Exception::class);
        $this->expectExceptionMessage('Percentile should be between 0 and 100, -146 given');
        Stats::percentile([1, 2, 3], -146);
    }

    public function testMedian(): void
    {
        isSame(0.0, Stats::median([]));
        isSame(1.0, Stats::median([1]));
        isSame(1.5, Stats::median([1, 2]));
        isSame(5.5, Stats::median([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]));
        isSame(5.5, Stats::median([1, 1, 1, 1, 5, 6, 7, 8, 9, 10]));
    }
}
