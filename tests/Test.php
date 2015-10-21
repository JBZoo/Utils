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
 */

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Package;
use JBZoo\Utils\Exception;

/**
 * Class Test
 * @package JBZoo\PHPUnit
 */
class Test extends PHPUnit
{

    public function testShouldDoSomeStreetMagic()
    {
        $obj = new Package();
        is('street magic', $obj->doSomeStreetMagic());
    }

    /**
     * @expectedException \JBZoo\Utils\Exception
     */
    public function testShouldShowException()
    {
        throw new Exception('Test message');
    }
}
