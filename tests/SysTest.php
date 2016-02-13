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

    public function testIsPHPVersion()
    {
        isFalse(Sys::isPHP('5.3', '4'));
        isFalse(Sys::isPHP('5.3', '4.0'));
        isFalse(Sys::isPHP('5.3', '5'));
        isFalse(Sys::isPHP('5.3', '5.0'));
        isFalse(Sys::isPHP('5.3', '5.2'));

        isTrue(Sys::isPHP('5.3', '5.3'));
        isTrue(Sys::isPHP('5.3', '5.3.0'));
        isTrue(Sys::isPHP('5.3', '5.3.1'));
        isTrue(Sys::isPHP('5.3', '5.3.17'));

        isFalse(Sys::isPHP('5.3', '5.4'));
        isFalse(Sys::isPHP('5.3', '5.4.0'));
        isFalse(Sys::isPHP('5.3', '5.4.1'));

        isFalse(Sys::isPHP('5.3', '5.5'));
        isFalse(Sys::isPHP('5.3', '5.5.0'));
    }
}