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

use \DateTime;
use \DateTimeZone;
use JBZoo\Utils\Dates;

/**
 * Class DatesTest
 * @package JBZoo\PHPUnit
 */
class DatesTest extends PHPUnit
{
    protected function setUp()
    {
        date_default_timezone_set('UTC');
    }

    public function testToStamp()
    {
        is('1446203259', Dates::toStamp(new \DateTime('2015-10-30 11:07:39')));
        is(0, Dates::toStamp('undefined date', false));

        isTrue(is_numeric(Dates::toStamp(null)));

        $time = time();
        is($time, Dates::toStamp());
        is($time, Dates::toStamp($time));
        isTrue(is_numeric(Dates::toStamp('+1 week')));
        isTrue(is_numeric(Dates::toStamp(new DateTime())));
    }

    /**
     * Test that factory() returns a DateTime object.
     */
    public function testFactory()
    {
        isClass('\DateTime', Dates::factory());
        isClass('\DateTime', Dates::factory('1988-02-26 12:23:12'));
        isClass('\DateTime', Dates::factory(time()));

        $dt = new DateTime();
        same($dt, Dates::factory($dt));
    }

    public function testTimezone()
    {
        isClass('\DateTimeZone', Dates::timezone());

        $dtz = new DateTimeZone('America/Los_Angeles');
        same($dtz, Dates::timezone($dtz));
    }

    public function testSql()
    {
        $format = Dates::SQL_FORMAT;

        is(date($format, time()), Dates::sql());
        is(date($format, time()), Dates::sql(''));
        is(date($format, time()), Dates::sql(0));
        is(date($format, time()), Dates::sql(null));
        is(date($format, time()), Dates::sql(false));

        is(date($format, time()), Dates::sql('string'));
        is('2015-10-30 11:07:39', Dates::sql('1446203259'));
        is('2015-10-30 11:07:39', Dates::sql(1446203259));
        is('2015-10-30 11:07:39', Dates::sql('2015-10-30 11:07:39'));
        is('2015-10-30 00:00:00', Dates::sql('2015-10-30'));
    }

    public function testIsDate()
    {
        isFalse(Dates::is(''));
        isFalse(Dates::is(null));
        isFalse(Dates::is(false));
        // isFalse(Dates::is('-0100')); // WAT????
        isFalse(Dates::is('string'));
        isFalse(Dates::is('1446203259'));
        isFalse(Dates::is(1446203259));

        isTrue(Dates::is('now'));
        isTrue(Dates::is('2015-10-30'));
        isTrue(Dates::is('2015-10-30 11:07:39'));
    }

    public function testHuman()
    {
        is('30 Oct 2015 00:00', Dates::human('2015-10-30'));
        is('30 October 2015', Dates::human('2015-10-30', 'd F Y'));
        is('30 Oct 2015', Dates::human('2015-10-30', 'd M Y'));
    }

    public function testIsThisWeek()
    {
        isTrue(Dates::isThisWeek('+0 week'));
        isFalse(Dates::isThisWeek('+2 week'));
        isFalse(Dates::isThisWeek('-2 week'));
    }

    public function testIsThisMonth()
    {
        isTrue(Dates::isThisMonth('+0 month'));
        isFalse(Dates::isThisMonth('+2 month'));
        isFalse(Dates::isThisMonth('-2 month'));
    }

    public function testIsThisYear()
    {
        isTrue(Dates::isThisYear('+0 year'));
        isFalse(Dates::isThisYear('+2 year'));
        isFalse(Dates::isThisYear('-2 year'));
    }

    public function testIsTomorrow()
    {
        isTrue(Dates::isTomorrow('+1 day'));
        isFalse(Dates::isTomorrow('+0 day'));
        isFalse(Dates::isTomorrow('-1 day'));
    }

    public function testIsToday()
    {
        isTrue(Dates::isToday('+0 day'));
        isFalse(Dates::isToday('+2 day'));
        isFalse(Dates::isToday('-2 day'));
    }

    public function testIsYesterday()
    {
        isTrue(Dates::isYesterday('-1 day'));
        isFalse(Dates::isYesterday('+0 day'));
        isFalse(Dates::isYesterday('+1 day'));
    }

    public function testConst()
    {
        isSame('Y-m-d H:i:s', Dates::SQL_FORMAT);
        isSame('0000-00-00 00:00:00', Dates::SQL_NULL);
    }
}