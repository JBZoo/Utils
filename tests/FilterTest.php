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

use JBZoo\Utils\Filter;

/**
 * Class FilterTest
 * @package JBZoo\PHPUnit
 */
class FilterTest extends PHPUnit
{
    /**
     * @param $exepted
     * @param $actual
     * @dataProvider providerInt
     */
    public function testInt($exepted, $actual)
    {
        isSame($exepted, Filter::_($actual, 'int'));
    }

    public function providerInt()
    {
        return array(
            array(0, null),
            array(0, false),
            array(0, ''),
            array(0, 0),
            array(1, 1),
            array(1, '1'),
            array(1, '01'),
            array(-1, '-01'),
            array(-15, ' - 1 5 '),
            array(-17, ' - 1 asd 7 '),
            array(-1, ' - 1 . 0 '),
            array(-1, ' - 1 , 5 '),
            array(-1, ' - 1 - 0 '),
            array(3, ' + 3'),
            array(-4, ' - 4'),
            array(-5, ' +- 5'),
            array(6, ' -+ 6'),
        );
    }

    /**
     * @param $exepted
     * @param $actual
     * @param $round
     * @dataProvider providerFloat
     */
    public function testFloat($exepted, $actual, $round = null)
    {
        if (null === $round) {
            is($exepted, Filter::_($actual, 'float'));
        } else {
            is($exepted, Filter::float($actual, $round));
        }
    }

    public function providerFloat()
    {
        return array(
            array(0.0, null),
            array(0.0, false),
            array(0.0, ''),
            array(0.0, 'asdasd'),
            array(0.0, 0),
            array(1.0, 1),
            array(123456789.0, 123456789),
            array(1.0, '1'),
            array(1.0, '01'),
            array(-1.0, '-01'),
            array(-10.0, ' - 1 0 '),
            array(-1.5, ' - 1,5 '),
            array(-1.5, ' - 1.5 '),
            array(-1.512, ' - 1.5123 ', 3),
            array(-15123.0, ' - 1 asd 5123 ', 3),
            array(15123.0, ' + 1 asd 5123 ', 3),

            array(-12.451, 'abc-12,451'),
            array(-12.452, 'abc-12.452'),
            array(-12.453, '-abc12.453'),
            array(-12.454, 'abc-12.454abc'),
            array(-12.455, 'abc-12. 455'),
            array(-12.456, 'abc-12. 456 .7'),
            array(27.3e-34, '27.3e-34'),
        );
    }

    /**
     * @param $excepted
     * @param $actual
     * @dataProvider providerBool
     */
    public function testBool($excepted, $actual)
    {
        isSame($excepted, Filter::_($actual, 'bool'));
    }

    public function providerBool()
    {
        return array(
            array(true, '1'),
            array(true, ' 1'),
            array(true, '1 '),
            array(true, '10'),
            array(true, '-1'),
            array(true, true),
            array(true, 27),
            array(true, 1.0),
            array(true, -1),
            array(true, -1.0),
            array(true, 10),
            array(true, 10.0),
            array(true, 10.0),
            array(true, 'true'),
            array(true, 'TRUE'),
            array(true, 'yes'),
            array(true, 'YES'),
            array(true, 'y'),
            array(true, 'Y'),
            array(true, 'oui'),
            array(true, 'vrai'),
            array(true, 'ДА'),
            array(true, 'Д'),

            array(false, ''),
            array(false, ' '),
            array(false, ' 0'),
            array(false, '0 '),
            array(false, false),
            array(false, null),
            array(false, 0),
            array(false, '0'),
            array(false, '0.'),
            array(false, '0.0'),
            array(false, '0.00'),
            array(false, 'false'),
            array(false, 'no'),
            array(false, 'n'),
            array(false, 'non'),
            array(false, 'faux'),
            array(false, 'НЕТ'),
        );
    }

    /**
     * @param $excepted
     * @param $actual
     * @dataProvider providerEmail
     */
    public function testEmail($excepted, $actual)
    {
        if ($excepted) {
            isTrue(Filter::_($actual, 'email'));
        } else {
            isFalse(Filter::_($actual, 'email'));
        }
    }

    public function providerEmail()
    {
        return array(
            array(true, 'john.smith@gmail.com'),
            array(true, 'john.smith+label@gmail.com'),
            array(true, 'john.smith@gmail.co.uk'),
            array(true, '_somename@example.com'),

            array(false, 'русская@почта.рф'), // madness..
            array(false, '"Abc\@def"@example.com'),
            array(false, '"Fred Bloggs"@example.com'),
            array(false, '"Joe\\Blow"@example.com'),
            array(false, '"Abc@def"@example.com'),
            array(false, '\$A12345@example.com'),
        );
    }

    public function testDigets()
    {
        $string = " 0 1 a2b 3c!@#$%^&*()-= <>\t";

        is('0123', Filter::_($string, 'digits'));
        is('abc', Filter::_($string, 'alpha'));
        is('01a2b3c', Filter::_($string, 'alphanum'));
    }

    public function testBase64()
    {
        $string = '+/0123456789=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        is($string, Filter::_($string, 'base64'));
    }

    public function testTrim()
    {
        is('multi', Filter::_("\n\r" . ' multi　' . chr(0xE3) . chr(0x80) . chr(0x80), 'trim'));
        is('multi', Filter::_(chr(0xC2) . chr(0xA0) . "\n\r" . ' multi　' . "\t", 'trim'));

        is('clean', Filter::_('clean', 'trim'));
    }

    public function testPath()
    {
        is('', Filter::_(false, 'path'));
        is('', Filter::_('http://www.fred.com/josephus', 'path'));
        is('images/system', Filter::_('images/system', 'path'));
        is('/images/system', Filter::_('/images/system', 'path'));
    }

    public function testArray()
    {
        $object = (object)array('p' => 'PPP', 'i' => 'III', 'z' => '', 'w' => 123);

        is(array('p' => 'PPP', 'i' => 'III', 'z' => '', 'w' => 123), Filter::_($object, 'arr'));
        is(array('p' => 'PPP', 'i' => 'III', 'w' => 123), Filter::arr($object, 'noempty'));
        is(array('z' => '', 'w' => 123), Filter::arr($object, function ($value, $key) {
            return ($key === 'z' || $value === 123) ? true : false;
        }));
    }

    public function testCmd()
    {
        $excepted = '0123456789-abcdefghijklmnopqrstuvwxyz_abcdefghijklmnopqrstuvwxyz';
        $string   = ' 0123456789-ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz ';

        is($excepted, Filter::_($string, 'cmd'));
    }

    public function testString()
    {
        is('some word', Filter::_(' <img> some word ' . "\t", 'strip'));
    }

    public function testAlias()
    {
        is('some-word', Filter::_(' <img> some word ' . "\t", 'alias'));
    }

    public function testApply()
    {
        $source = ' <img> some-WORD ' . "\t";

        is($source, Filter::_($source));
        is($source, Filter::_($source, ''));
        is($source, Filter::_($source, 'raw'));

        $excepted = Filter::strip($source);
        is($excepted, Filter::_($source, 'strip'));

        $excepted = Filter::strip($source);
        $excepted = Filter::cmd($excepted);
        is($excepted, Filter::_($source, 'Strip, CMD'));
    }

    /**
     * @expectedException \JBZoo\PHPUnit\Exception
     */
    public function testApplyUnderfined()
    {
        Filter::_('123', 'qwertY');
    }

    public function testApplyFunction()
    {
        $source = 'some-WORD';

        is('some_WORD', Filter::_($source, function ($value) {
            $value = str_replace('-', '_', $value);
            return $value;
        }));
    }
}
