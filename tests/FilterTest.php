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

use JBZoo\Data\Data;
use JBZoo\Data\JSON;
use JBZoo\Utils\Filter;

class FilterTest extends PHPUnit
{
    /**
     * @dataProvider provideIntCases
     * @param mixed $exepted
     * @param mixed $actual
     */
    public function testInt($exepted, $actual): void
    {
        isSame($exepted, Filter::_($actual, 'int'));
    }

    /**
     * @dataProvider provideFloatCases
     * @param mixed      $excepted
     * @param mixed      $actual
     * @param null|mixed $round
     */
    public function testFloat($excepted, $actual, $round = null): void
    {
        if ($round === null) {
            isSame($excepted, Filter::_($actual, 'float'));
        } else {
            isSame($excepted, Filter::float($actual, $round));
        }
    }

    /**
     * @dataProvider provideBoolCases
     * @param mixed $excepted
     * @param mixed $actual
     */
    public function testBool($excepted, $actual): void
    {
        isSame($excepted, Filter::_($actual, 'bool'));
    }

    public function testDigests(): void
    {
        $string = " 0 1 a2b 3c!@#$%^&*()-= <>\t";

        isSame('0123', Filter::_($string, 'digits'));
        isSame('abc', Filter::_($string, 'alpha'));
        isSame('01a2b3c', Filter::_($string, 'alphanum'));
    }

    public function testBase64(): void
    {
        $string = '+/0123456789=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        isSame($string, Filter::_($string, 'base64'));
    }

    public function testTrimExtend(): void
    {
        isSame('multi', Filter::_("\n\r" . ' multi　' . \chr(0xE3) . \chr(0x80) . \chr(0x80), 'trimExtend'));
        isSame('multi', Filter::_(\chr(0xC2) . \chr(0xA0) . "\n\r" . ' multi　' . "\t", 'trimExtend'));

        isSame('clean', Filter::_('clean', 'trim'));
    }

    public function testPath(): void
    {
        isSame('', Filter::_('', 'path'));
        isSame('', Filter::_('http://www.fred.com/josephus', 'path'));
        isSame('images/system', Filter::_('images/system', 'path'));
        isSame('/images/system', Filter::_('/images/system', 'path'));
    }

    public function testArray(): void
    {
        $object = (object)['p' => 'PPP', 'i' => 'III', 'z' => '', 'w' => 123];

        isSame(['p' => 'PPP', 'i' => 'III', 'z' => '', 'w' => 123], Filter::_($object, 'arr'));
        isSame(['p' => 'PPP', 'i' => 'III', 'w' => 123], Filter::arr($object, 'noempty'));
        isSame(['w' => 123], Filter::arr($object, static fn ($value) => $value === 123));
    }

    public function testCmd(): void
    {
        $excepted = '0123456789-abcdefghijklmnopqrstuvwxyz_abcdefghijklmnopqrstuvwxyz';
        $string   = ' 0123456789-ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz  йцуке ';

        isSame($excepted, Filter::_($string, 'cmd'));
    }

    public function testString(): void
    {
        isSame('some word', Filter::_(' <img> some word ' . "\t", 'strip'));
    }

    public function testAlias(): void
    {
        isSame('some-word', Filter::_(' <img> some word ' . "\t", 'alias'));
    }

    public function testApplyRaw(): void
    {
        $source = ' <img> some-<b>WORD</b> ' . "\t";

        isSame($source, Filter::_($source));
        isSame($source, Filter::_($source, ''));
        isSame($source, Filter::_($source, 'raw'));
        isSame($source, Filter::_($source, 'RAW'));
        isSame($source, Filter::_($source, ' R A W '));
    }

    public function testParseLines(): void
    {
        $source   = " qw\rer\n ty \r\n12\n\r34 ";
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

    public function testOthers(): void
    {
        isSame('low', Filter::_(' LOW ', 'low'));
        isSame('UP', Filter::_(' up ', 'up'));
        isSame('spaces', Filter::_(' s p a c e s ', 'stripSpace'));
        isSame('denis !@#$%^&*()_ 1234567890 qwerty', Filter::_(' Денис !@#$%^&*()_ 1234567890 qwerty ', 'clean'));
        isSame(' One &amp; Two &lt;&gt; &amp;mdash; ', Filter::_(' One & Two <> &mdash; ', 'html'));
        isSame(' One &amp; Two &lt;&gt; &amp;mdash; ', Filter::_(' One & Two <> &mdash; ', 'xml'));
        isSame(' One &amp; Two &lt;&gt; &amp;mdash; ', Filter::_(' One & Two <> &mdash; ', 'esc'));
    }

    public function testApplyOneRule(): void
    {
        $source = '127.0001 <img> some-<b>WORD</b> ' . "\t";

        $excepted = Filter::strip($source);
        isSame($excepted, Filter::_($source, 'strip'));
    }

    public function testApplySeveralRules(): void
    {
        $source = '127.0001 <img> some-<b>WORD</b> ' . "\t";

        $excepted = Filter::strip($source);
        $excepted = Filter::alias($excepted);
        $excepted = Filter::int($excepted);
        isSame($excepted, Filter::_($source, 'Strip, alias,int'));
    }

    public function testApplyUnderfinedRule(): void
    {
        $this->expectException(\JBZoo\Utils\Exception::class);

        Filter::_('123', 'qwertY');
    }

    public function testApplyFunction(): void
    {
        $source = 'some-WORD';

        isSame(
            'some_WORD',
            Filter::_($source, static fn ($value) => \str_replace('-', '_', $value)),
        );
    }

    public function testUcfirst(): void
    {
        isSame('Test', Filter::ucFirst('test'));
        isSame('Test', Filter::ucFirst('Test'));
        isSame('Test', Filter::ucFirst('TEST'));
        isSame('Test', Filter::ucFirst('tEST'));
    }

    public function testClassname(): void
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

    public function tstStripQuotes(): void
    {
        isSame('qwerty', Filter::stripQuotes('qwerty'));
        isSame('qwerty"', Filter::stripQuotes('qwerty"'));
        isSame('"qwerty', Filter::stripQuotes('"qwerty'));
        isSame('"qwerty"', Filter::stripQuotes('"qwerty"'));

        isSame('qwerty', Filter::stripQuotes('qwerty'));
        isSame("qwerty'", Filter::stripQuotes('qwerty\''));
        isSame("'qwerty", Filter::stripQuotes('\'qwerty'));
        isSame("'qwerty'", Filter::stripQuotes('\'qwerty\''));

        isSame("'qwerty\"", Filter::stripQuotes('\'qwerty"'));
        isSame("\"qwerty'", Filter::stripQuotes('"qwerty\''));
    }

    public function testData(): void
    {
        $data = [
            'key' => 'value',
        ];

        $obj = new Data($data);

        isSame($obj, Filter::data($obj));
        isSame($data, (array)Filter::data($obj));
        isSame($data, (array)Filter::data($data));
    }

    public function testJson(): void
    {
        $data = [
            'key' => 'value',
        ];

        $obj = new JSON($data);

        isSame($obj, Filter::json($obj));
        isSame($data, (array)Filter::json($obj));
        isSame($data, (array)Filter::json($data));
    }

    public static function provideIntCases(): iterable
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

    public static function provideFloatCases(): iterable
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
            [2.6e-19, '26.3e-20', 20],
            [2.53E-19, '25.3e-20', 100],
            [2.4e-9, '24.3e-10'],
        ];
    }

    public static function provideBoolCases(): iterable
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
}
