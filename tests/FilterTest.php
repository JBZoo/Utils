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

use JBZoo\Data\JSON;
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
            isSame($exepted, Filter::_($actual, 'float'));
        } else {
            isSame($exepted, Filter::float($actual, $round));
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
            array(true, '*'),
            array(true, '+'),
            array(true, '++'),
            array(true, '+++'),
            array(true, '++++'),
            array(true, '+++++'),

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
            array(false, '-'),
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

        isSame('0123', Filter::_($string, 'digits'));
        isSame('abc', Filter::_($string, 'alpha'));
        isSame('01a2b3c', Filter::_($string, 'alphanum'));
    }

    public function testBase64()
    {
        $string = '+/0123456789=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        isSame($string, Filter::_($string, 'base64'));
    }

    public function testTrimExtend()
    {
        isSame('multi', Filter::_("\n\r" . ' multi　' . chr(0xE3) . chr(0x80) . chr(0x80), 'trimExtend'));
        isSame('multi', Filter::_(chr(0xC2) . chr(0xA0) . "\n\r" . ' multi　' . "\t", 'trimExtend'));

        isSame('clean', Filter::_('clean', 'trim'));
    }

    public function testPath()
    {
        isSame('', Filter::_(false, 'path'));
        isSame('', Filter::_('http://www.fred.com/josephus', 'path'));
        isSame('images/system', Filter::_('images/system', 'path'));
        isSame('/images/system', Filter::_('/images/system', 'path'));
    }

    public function testArray()
    {
        $object = (object)array('p' => 'PPP', 'i' => 'III', 'z' => '', 'w' => 123);

        isSame(array('p' => 'PPP', 'i' => 'III', 'z' => '', 'w' => 123), Filter::_($object, 'arr'));
        isSame(array('p' => 'PPP', 'i' => 'III', 'w' => 123), Filter::arr($object, 'noempty'));
        isSame(array('w' => 123), Filter::arr($object, function ($value) {
            return ($value === 123) ? true : false;
        }));
    }

    public function testCmd()
    {
        $excepted = '0123456789-abcdefghijklmnopqrstuvwxyz_abcdefghijklmnopqrstuvwxyz';
        $string   = ' 0123456789-ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz  йцуке ';

        isSame($excepted, Filter::_($string, 'cmd'));
    }

    public function testString()
    {
        isSame('some word', Filter::_(' <img> some word ' . "\t", 'strip'));
    }

    public function testAlias()
    {
        isSame('some-word', Filter::_(' <img> some word ' . "\t", 'alias'));
    }

    public function testApplyRaw()
    {
        $source = ' <img> some-<b>WORD</b> ' . "\t";

        isSame($source, Filter::_($source));
        isSame($source, Filter::_($source, ''));
        isSame($source, Filter::_($source, 'raw'));
        isSame($source, Filter::_($source, 'RAW'));
        isSame($source, Filter::_($source, ' R A W '));
    }

    public function testParseLines()
    {
        $source   = " qw\rer\n ty \r\n12\n\r34 ";
        $expected = array(
            'qw' => 'qw',
            'er' => 'er',
            'ty' => 'ty',
            '12' => '12',
            '34' => '34'
        );

        isSame($expected, Filter::parseLines($source));
        isSame($expected, Filter::parseLines(array($source)));
    }

    public function testOthers()
    {
        isSame('low', Filter::_(' LOW ', 'low'));
        isSame('UP', Filter::_(' up ', 'up'));
        isSame('spaces', Filter::_(' s p a c e s ', 'stripSpace'));
        isSame('denis !@#$%^&*()_ 1234567890 qwerty', Filter::_(' Денис !@#$%^&*()_ 1234567890 qwerty ', 'clean'));
        isSame(' One &amp; Two &lt;&gt; &amp;mdash; ', Filter::_(' One & Two <> &mdash; ', 'html'));
        isSame(' One &amp; Two &lt;&gt; &amp;mdash; ', Filter::_(' One & Two <> &mdash; ', 'xml'));
        isSame(' One &amp; Two &lt;&gt; &amp;mdash; ', Filter::_(' One & Two <> &mdash; ', 'esc'));
    }

    public function testApplyOneRule()
    {
        $source = '127.0001 <img> some-<b>WORD</b> ' . "\t";

        $excepted = Filter::strip($source);
        isSame($excepted, Filter::_($source, 'strip'));
    }

    public function testApplySeveralRules()
    {
        $source = '127.0001 <img> some-<b>WORD</b> ' . "\t";

        $excepted = Filter::strip($source);
        $excepted = Filter::alias($excepted);
        $excepted = Filter::int($excepted);
        isSame($excepted, Filter::_($source, 'Strip, alias,int'));
    }

    /**
     * @expectedException \JBZoo\Utils\Exception
     */
    public function testApplyUnderfinedRule()
    {
        Filter::_('123', 'qwertY');
    }

    public function testApplyFunction()
    {
        $source = 'some-WORD';

        isSame('some_WORD', Filter::_($source, function ($value) {
            $value = str_replace('-', '_', $value);
            return $value;
        }));
    }

    public function testUcfirst()
    {
        isSame('Test', Filter::ucfirst('test'));
        isSame('Test', Filter::ucfirst('Test'));
        isSame('Test', Filter::ucfirst('TEST'));
        isSame('Test', Filter::ucfirst('tEST'));
    }

    public function testClassname()
    {
        isSame('Class123Name456', Filter::className('Class123Name456'));
        isSame('Class123Name456', Filter::className('Class123 Name456'));
        isSame('Class123Name456', Filter::className('CLASS123 NAME456'));
        isSame('Class123Name456', Filter::className('class123 name456'));
        isSame('Class123Name456', Filter::className('class123Name456'));
        isSame('Class123Name456', Filter::className('class123_Name456'));
        isSame('Class123Name456', Filter::className('class123_name456'));
        isSame('Class123Name456', Filter::className('class123-name456'));
        isSame('Class123Name456', Filter::className('class123|name456'));
        isSame('Class123Name456', Filter::className('class123.name456'));
        isSame('Class123Name456', Filter::className('class123name456'));
        isSame('Class123Name456', Filter::className('CLASS123NAME456'));
        isSame('Classname', Filter::className('CLASSNAME'));
    }

    public function tstStripQuotes()
    {
        isSame('qwerty', Filter::stripQuotes('qwerty'));
        isSame('qwerty"', Filter::stripQuotes('qwerty"'));
        isSame('"qwerty', Filter::stripQuotes('"qwerty'));
        isSame('"qwerty"', Filter::stripQuotes('"qwerty"'));

        isSame("qwerty", Filter::stripQuotes('qwerty'));
        isSame("qwerty'", Filter::stripQuotes('qwerty\''));
        isSame("'qwerty", Filter::stripQuotes('\'qwerty'));
        isSame("'qwerty'", Filter::stripQuotes('\'qwerty\''));

        isSame("'qwerty\"", Filter::stripQuotes('\'qwerty"'));
        isSame("\"qwerty'", Filter::stripQuotes('"qwerty\''));
    }

    public function testData()
    {
        $data = array(
            'key' => 'value',
        );

        $obj = new JSON($data);

        isSame($obj, Filter::data($obj));
        isSame($data, (array)Filter::data($obj));
        isSame($data, (array)Filter::data($data));
    }
}
