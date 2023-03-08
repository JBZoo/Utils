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

use DateTime;
use JBZoo\Utils\Dates;

class DatesTest extends PHPUnit
{
    protected function setUp(): void
    {
        \date_default_timezone_set('UTC');
    }

    public function testToStamp(): void
    {
        is('1446203259', Dates::toStamp(new \DateTime('2015-10-30 11:07:39')));
        is(0, Dates::toStamp('undefined date', false));

        isTrue(\is_numeric(Dates::toStamp()));

        $time = \time();
        is($time, Dates::toStamp());
        is($time, Dates::toStamp($time));
        isTrue(\is_numeric(Dates::toStamp('+1 week')));
        isTrue(\is_numeric(Dates::toStamp(new \DateTime())));
    }

    /**
     * Test that factory() returns a DateTime object.
     */
    public function testFactory(): void
    {
        isClass(\DateTime::class, Dates::factory());
        isClass(\DateTime::class, Dates::factory('1988-02-26 12:23:12'));
        isClass(\DateTime::class, Dates::factory(\time()));

        $datetime = new \DateTime();
        isSame($datetime, Dates::factory($datetime));
    }

    public function testTimezone(): void
    {
        isClass(\DateTimeZone::class, Dates::timezone());

        $dtz = new \DateTimeZone('America/Los_Angeles');
        isSame($dtz, Dates::timezone($dtz));
    }

    public function testSql(): void
    {
        $format = Dates::SQL_FORMAT;

        is(\date($format), Dates::sql());
        is(\date($format), Dates::sql(''));
        is(\date($format), Dates::sql(0));
        is(\date($format), Dates::sql());
        is(\date($format), Dates::sql(null));

        is(\date($format), Dates::sql('string'));
        is('2015-10-30 11:07:39', Dates::sql('1446203259'));
        is('2015-10-30 11:07:39', Dates::sql(1446203259));
        is('2015-10-30 11:07:39', Dates::sql('2015-10-30 11:07:39'));
        is('2015-10-30 00:00:00', Dates::sql('2015-10-30'));
    }

    public function testIsDate(): void
    {
        isFalse(Dates::is(''));
        isFalse(Dates::is(null));
        isFalse(Dates::is('false'));
        // isFalse(Dates::is('-0100')); // WAT????
        isFalse(Dates::is('string'));
        isFalse(Dates::is('1446203259'));

        isTrue(Dates::is('now'));
        isTrue(Dates::is('2015-10-30'));
        isTrue(Dates::is('2015-10-30 11:07:39'));
    }

    public function testHuman(): void
    {
        is('30 Oct 2015 00:00', Dates::human('2015-10-30'));
        is('30 October 2015', Dates::human('2015-10-30', 'd F Y'));
        is('30 Oct 2015', Dates::human('2015-10-30', 'd M Y'));
    }

    public function testIsThisWeek(): void
    {
        isTrue(Dates::isThisWeek('+0 week'));
        isFalse(Dates::isThisWeek('+2 week'));
        isFalse(Dates::isThisWeek('-2 week'));
    }

    public function testIsThisMonth(): void
    {
        isTrue(Dates::isThisMonth('+0 month'));
        isFalse(Dates::isThisMonth('+2 month'));
        isFalse(Dates::isThisMonth('-2 month'));
    }

    public function testIsThisYear(): void
    {
        isTrue(Dates::isThisYear('+0 year'));
        isFalse(Dates::isThisYear('+2 year'));
        isFalse(Dates::isThisYear('-2 year'));
    }

    public function testIsTomorrow(): void
    {
        isTrue(Dates::isTomorrow('+1 day'));
        isFalse(Dates::isTomorrow('+0 day'));
        isFalse(Dates::isTomorrow('-1 day'));
    }

    public function testIsToday(): void
    {
        isTrue(Dates::isToday('+0 day'));
        isFalse(Dates::isToday('+2 day'));
        isFalse(Dates::isToday('-2 day'));
    }

    public function testIsYesterday(): void
    {
        isTrue(Dates::isYesterday('-1 day'));
        isFalse(Dates::isYesterday('+0 day'));
        isFalse(Dates::isYesterday('+1 day'));
    }

    public function testConst(): void
    {
        isSame('Y-m-d H:i:s', Dates::SQL_FORMAT);
        isSame('0000-00-00 00:00:00', Dates::SQL_NULL);
    }

    public function testTimeFormat(): void
    {
        isSame('0.000 sec', Dates::formatTime(0));
        isSame('0.568 sec', Dates::formatTime(0.56789));
        isSame('1.568 sec', Dates::formatTime(1.56789));
        isSame('1.999 sec', Dates::formatTime(1.999));
        isSame('00:00:03', Dates::formatTime(2.56789));
        isSame('00:00:02', Dates::formatTime(2));
        isSame('00:00:50', Dates::formatTime(50));
        isSame('00:00:00', Dates::formatTime(0, 0));
        isSame('00:00:01', Dates::formatTime(1, 0));
        isSame('00:00:02', Dates::formatTime(2, 0));
        isSame('00:00:02', Dates::formatTime(1.9999, 0));
    }
}
