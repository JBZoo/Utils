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

declare(strict_types=1);

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Csv;

/**
 * Class CsvTest
 *
 * @package JBZoo\PHPUnit
 */
class CsvTest extends PHPUnit
{
    public function testParse()
    {
        /** @noinspection ArgumentEqualsDefaultValueInspection */
        $result = Csv::parse(__DIR__ . '/resources/parse.csv', ';', '"', true);

        isSame([
            [
                'id'       => '1',
                'name'     => 'qwerty',
                'some num' => '42',
            ],
            [
                'id'       => '2',
                'name'     => 'Some word',
                'some num' => '4242',
            ],
        ], $result);

        $result = Csv::parse(__DIR__ . '/resources/parse.csv', ';', '"', false);
        isSame([
            ['id', 'name', 'some num'],
            ['1', 'qwerty', '42'],
            ['2', 'Some word', '4242'],
        ], $result);
    }
}
