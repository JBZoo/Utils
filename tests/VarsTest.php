<?php
/**
 * JBZoo Utils
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Utils
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Utils
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Vars;

/**
 * Class VarsTest
 *
 * @package JBZoo\PHPUnit
 */
class VarsTest extends PHPUnit
{
    public function testIn()
    {
        isTrue(Vars::isIn(0.5, 0, 5));
        isTrue(Vars::isIn(0.5, 0.5, 5));
        isFalse(Vars::isIn(0.5, 1, 5));
    }

    public function testLimit()
    {
        is(100, Vars::limit(125, 50, 100));
        is(50, Vars::limit(45, 50, 100));
        is(77, Vars::limit(77, 50, 100));
    }

    public function testMin()
    {
        is(50, Vars::min(12, 50));
        is(55, Vars::min(55, 50));
        is(123, Vars::min(123, 50));
    }

    public function testMax()
    {
        is(-15, Vars::max(-15, 50));
        is(12, Vars::max(12, 50));
        is(50, Vars::max(55, 50));
        is(50, Vars::max(123, 50));
    }

    public function testOut()
    {
        isTrue(Vars::out(15, 5, 10));
        isTrue(Vars::out(3, 5, 10));
        isFalse(Vars::out(8, 5, 10));
    }

    public function testIsEven()
    {
        isTrue(Vars::isEven(2));
        isTrue(Vars::isEven(88));
        isTrue(Vars::isEven(62.3));
        isFalse(Vars::isEven(9));
        isFalse(Vars::isEven(17));
        isFalse(Vars::isEven(47.9));
    }

    public function testIsNegative()
    {
        isTrue(Vars::isNegative(-1));
        isTrue(Vars::isNegative(-384));
        isFalse(Vars::isNegative(0));
        isFalse(Vars::isNegative(34));
    }

    public function testIsOdd()
    {
        isFalse(Vars::isOdd(2));
        isFalse(Vars::isOdd(88));
        isFalse(Vars::isOdd(62.3));
        isTrue(Vars::isOdd(9));
        isTrue(Vars::isOdd(17));
        isTrue(Vars::isOdd(47.9));
    }

    public function testIsPositive()
    {
        isTrue(Vars::isPositive(343));
        isTrue(Vars::isPositive(79));
        isTrue(Vars::isPositive(0));
        isFalse(Vars::isPositive(0, false)); // don't include 0
        isFalse(Vars::isPositive(-1));
    }

    public function testRelativePercent()
    {
        isSame('200', Vars::relativePercent(50, 100));
        isSame('33', Vars::relativePercent(150, 50));
        isSame('300', Vars::relativePercent(50, 150));
        isSame('100', Vars::relativePercent(100, 100));
        isSame('10 000', Vars::relativePercent(1, 100));
        isSame('1', Vars::relativePercent(100, 1));
        isSame('100', Vars::relativePercent(0, 1));
    }
}