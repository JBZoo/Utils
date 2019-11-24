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

use JBZoo\Data\JSON;
use JBZoo\Utils\Filter;

/**
 * Class FilterTest
 *
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
        return [
            [0, null],
            [0, false],
            [0, ''],
            [0, 0],
            [1, 1],
            [1, '1'],
            [1, '01'],
            [-1, '-01'],
            [-15, ' - 1 5 '],
            [-17, ' - 1 asd 7 '],
            [-1, ' - 1 . 0 '],
            [-1, ' - 1 , 5 '],
            [-1, ' - 1 - 0 '],
            [3, ' + 3'],
            [-4, ' - 4'],
            [-5, ' +- 5'],
            [6, ' -+ 6'],
        ];
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
        return [
            [0.0, null],
            [0.0, false],
            [0.0, ''],
            [0.0, 'asdasd'],
            [0.0, 0],
            [1.0, 1],
            [123456789.0, 123456789],
            [1.0, '1'],
            [1.0, '01'],
            [-1.0, '-01'],
            [-10.0, ' - 1 0 '],
            [-1.5, ' - 1,5 '],
            [-1.5, ' - 1.5 '],
            [-1.512, ' - 1.5123 ', 3],
            [-15123.0, ' - 1 asd 5123 ', 3],
            [15123.0, ' + 1 asd 5123 ', 3],

            [-12.451, 'abc-12,451'],
            [-12.452, 'abc-12.452'],
            [-12.453, '-abc12.453'],
            [-12.454, 'abc-12.454abc'],
            [-12.455, 'abc-12. 455'],
            [-12.456, 'abc-12. 456 .7'],
            [27.3e-34, '27.3e-34'],
        ];
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
        return [
            [true, '1'],
            [true, ' 1'],
            [true, '1 '],
            [true, '10'],
            [true, '-1'],
            [true, true],
            [true, 27],
            [true, 1.0],
            [true, -1],
            [true, -1.0],
            [true, 10],
            [true, 10.0],
            [true, 10.0],
            [true, 'true'],
            [true, 'TRUE'],
            [true, 'yes'],
            [true, 'YES'],
            [true, 'y'],
            [true, 'Y'],
            [true, 'oui'],
            [true, 'vrai'],
            [true, 'ДА'],
            [true, 'Д'],
            [true, '*'],
            [true, '+'],
            [true, '++'],
            [true, '+++'],
            [true, '++++'],
            [true, '+++++'],

            [false, ''],
            [false, ' '],
            [false, ' 0'],
            [false, '0 '],
            [false, false],
            [false, null],
            [false, 0],
            [false, '0'],
            [false, '0.'],
            [false, '0.0'],
            [false, '0.00'],
            [false, 'false'],
            [false, 'no'],
            [false, 'n'],
            [false, 'non'],
            [false, 'faux'],
            [false, 'НЕТ'],
            [false, '-'],
        ];
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
        return [
            [true, 'john.smith@gmail.com'],
            [true, 'john.smith+label@gmail.com'],
            [true, 'john.smith@gmail.co.uk'],
            [true, '_somename@example.com'],

            [false, 'русская@почта.рф'], // madness..
            [false, '"Abc\@def"@example.com'],
            [false, '"Fred Bloggs"@example.com'],
            [false, '"Joe\\Blow"@example.com'],
            [false, '"Abc@def"@example.com'],
            [false, '\$A12345@example.com'],
        ];
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
        $object = (object)['p' => 'PPP', 'i' => 'III', 'z' => '', 'w' => 123];

        isSame(['p' => 'PPP', 'i' => 'III', 'z' => '', 'w' => 123], Filter::_($object, 'arr'));
        isSame(['p' => 'PPP', 'i' => 'III', 'w' => 123], Filter::arr($object, 'noempty'));
        isSame(['w' => 123], Filter::arr($object, function ($value) {
            return $value === 123;
        }));
    }

    public function testCmd()
    {
        $excepted = '0123456789-abcdefghijklmnopqrstuvwxyz_abcdefghijklmnopqrstuvwxyz';
        $string = ' 0123456789-ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz  йцуке ';

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
        $source = " qw\rer\n ty \r\n12\n\r34 ";
        $expected = [
            'qw' => 'qw',
            'er' => 'er',
            'ty' => 'ty',
            '12' => '12',
            '34' => '34',
        ];

        isSame($expected, Filter::parseLines($source));
        isSame($expected, Filter::parseLines([$source]));
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

    public function testApplyUnderfinedRule()
    {
        $this->expectException(\JBZoo\Utils\Exception::class);

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
        isSame('Test', Filter::ucFirst('test'));
        isSame('Test', Filter::ucFirst('Test'));
        isSame('Test', Filter::ucFirst('TEST'));
        isSame('Test', Filter::ucFirst('tEST'));
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
        $data = [
            'key' => 'value',
        ];

        $obj = new JSON($data);

        isSame($obj, Filter::data($obj));
        isSame($data, (array)Filter::data($obj));
        isSame($data, (array)Filter::data($data));
    }
}
