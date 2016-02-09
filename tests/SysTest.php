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

use JBZoo\Utils\Sys;

/**
 * Class SysTest
 * @package JBZoo\PHPUnit
 */
class SysTest extends PHPUnit
{
    public function testIsFunc()
    {
        isFalse(Sys::isFunc('qwerty'));
        isTrue(Sys::isFunc('trim'));
    }

    public function testSetTimeAndMemory()
    {
        Sys::setTime(1800);
        Sys::setMemory('128M');
    }
}