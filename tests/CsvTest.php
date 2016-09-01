<?php
/**
 * JBZoo Utils
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Utils
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/Utils
 * @author    Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Csv;

/**
 * Class CsvTest
 * @package JBZoo\PHPUnit
 */
class CsvTest extends PHPUnit
{
    public function testParse()
    {
        $result = Csv::parse(__DIR__ . '/resources/parse.csv', ';', '"', true);

        isSame(array(
            array(
                'id'       => '1',
                'name'     => 'qwerty',
                'some num' => '42',
            ),
            array(
                'id'       => '2',
                'name'     => 'Some word',
                'some num' => '4242',
            ),
        ), $result);

        $result = Csv::parse(__DIR__ . '/resources/parse.csv', ';', '"', false);
        isSame(array(
            array('id', 'name', 'some num'),
            array('1', 'qwerty', '42'),
            array('2', 'Some word', '4242'),
        ), $result);
    }
}
