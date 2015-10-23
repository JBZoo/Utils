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

use JBZoo\Utils\Vars;

/**
 * Class FilterTest
 * @package JBZoo\PHPUnit
 */
class FilterTest extends PHPUnit
{

    public function testInt()
    {
        same(0, Vars::int(null));
        same(0, Vars::int(0));
        same(1, Vars::int(1));
        same(1, Vars::int('1'));
        same(1, Vars::int('01'));
        same(-1, Vars::int('-01'));
        same(-10, Vars::int(' - 1 0 '));
    }

    public function testFloat()
    {
        same(0.0, Vars::float(null));
        same(0.0, Vars::float(0));
        same(1.0, Vars::float(1));
        same(1.0, Vars::float('1'));
        same(1.0, Vars::float('01'));
        same(-1.0, Vars::float('-01'));
        same(-10.0, Vars::float(' - 1 0 '));
        same(-1.5, Vars::float(' - 1,5 '));
        same(-1.5, Vars::float(' - 1.5 '));
        same(-1.512, Vars::float(' - 1.5123 ', 3));
    }

    public function testBool()
    {
        isTrue(Vars::bool('true'));
        isTrue(Vars::bool('yes'));
        isTrue(Vars::bool('y'));
        isTrue(Vars::bool('oui'));
        isTrue(Vars::bool('vrai'));
        isTrue(Vars::bool('ДА'));
        isTrue(Vars::bool('Д'));

        isFalse(Vars::bool('false'));
        isFalse(Vars::bool('no'));
        isFalse(Vars::bool('n'));
        isFalse(Vars::bool('non'));
        isFalse(Vars::bool('faux'));
        isFalse(Vars::bool('НЕТ'));

        isFalse(Vars::bool('test', false));
    }

    public function testEmail()
    {
        isTrue(Vars::email('john.smith@gmail.com'));
        isTrue(Vars::email('john.smith+label@gmail.com'));
        isTrue(Vars::email('john.smith@gmail.co.uk'));
        isFalse(Vars::email('русская@почта.рф')); // madness...
    }

    public function testDigets()
    {
        $string = " 0 1 a2b 3c!@#$%^&*()-= <>\t";

        is('0123', Vars::digets($string));
        is('abc', Vars::alpha($string));
        is('01a2b3c', Vars::alphaDigets($string));
    }
}
