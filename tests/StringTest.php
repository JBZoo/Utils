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

use JBZoo\Utils\Slug;
use JBZoo\Utils\Str;

use function JBZoo\Utils\isStrEmpty;

class StringTest extends PHPUnit
{
    public function testListToDescription(): void
    {
        $source = [
            '0'              => 'QWERTY',
            ''               => 'QWERTY123',
            'q'              => 123,
            'q123'           => 0,
            'qwerty'         => 123,
            'qwe'            => 123,
            'qweqwerty 1234' => '',
        ];

        isSame(
            \implode("\n", [
                'QWERTY',
                'QWERTY123',
                'Q: 123',
                'Q123: 0',
                'Qwerty: 123',
                'Qwe: 123',
                '',
            ]),
            Str::listToDescription($source),
        );

        isSame(
            \implode("\n", [
                'QWERTY',
                'QWERTY123',
                'Q     : 123',
                'Q123  : 0',
                'Qwerty: 123',
                'Qwe   : 123',
                '',
            ]),
            Str::listToDescription($source, true),
        );
    }

    public function testStrip(): void
    {
        $input  = ' The quick brown fox jumps over the lazy dog ';
        $expect = 'Thequickbrownfoxjumpsoverthelazydog';
        is($expect, Str::stripSpace($input));
    }

    public function testClean(): void
    {
        $input = ' <b>ASDF</b> !@#$%^&*()_+"\';:>< ';

        isSame('ASDF !@#$%^&*()_+"\';:><', Str::clean($input));
        isSame('asdf !@#$%^&*()_+\\"\\\';:><', Str::clean($input, true, true));
    }

    public function testParseLines(): void
    {
        isSame(['asd'], Str::parseLines('asd', false));
        isSame(['asd' => 'asd'], Str::parseLines('asd', true));
        isSame(['asd' => 'asd'], Str::parseLines('asd'));

        $lines = ['', false, 123, 456, ' 123   ', '      ', 'ASD', '0'];

        isSame([
            '123' => '123',
            '456' => '456',
            'ASD' => 'ASD',
            '0'   => '0',
        ], Str::parseLines(\implode("\r", $lines), true));

        isSame([
            '123' => '123',
            '456' => '456',
            'ASD' => 'ASD',
            '0'   => '0',
        ], Str::parseLines(\implode("\n", $lines), true));

        isSame([
            '123',
            '456',
            '123',
            'ASD',
            '0',
        ], Str::parseLines(\implode("\r\n", $lines), false));
    }

    public function testHtmlentities(): void
    {
        is('One &amp; Two &lt;&gt; &amp;mdash;', Str::htmlEnt('One & Two <> &mdash;'));
        is('One &amp; Two &lt;&gt; &mdash;', Str::htmlEnt('One &amp; Two <> &mdash;', true));
    }

    public function testUnique(): void
    {
        is(15, \strlen(Str::unique()));
        is(10, \strlen(Str::unique('t-')));
        isNotSame(Str::unique(), Str::unique());
        isNotSame(Str::unique(), Str::unique());
        isNotSame(Str::unique(), Str::unique());
    }

    public function testRandom(): void
    {
        is(10, \strlen(Str::random()));
        is(10, \strlen(Str::random(10)));
        is(10, \strlen(Str::random(10, true)));
        is(10, \strlen(Str::random(10, false)));

        isNotSame(Str::random(), Str::random());
        isNotSame(Str::random(), Str::random());
        isNotSame(Str::random(), Str::random());
    }

    public function testZeroPad(): void
    {
        is('0341', Str::zeroPad('0341', 1));
        is('341', Str::zeroPad('341', 3));
        is('0341', Str::zeroPad('341', 4));
        is('000341', Str::zeroPad('341', 6));
    }

    public function testTruncateSafe(): void
    {
        is('The quick brown fox...', Str::truncateSafe('The quick brown fox jumps over the lazy dog', 24));
        is(
            'The quick brown fox jumps over the lazy dog',
            Str::truncateSafe('The quick brown fox jumps over the lazy dog', 55),
        );
        is('Th...', Str::truncateSafe('The quick brown fox jumps over the lazy dog', 2));
        is('The...', Str::truncateSafe('The quick brown fox jumps over the lazy dog', 3));
        is('The...', Str::truncateSafe('The quick brown fox jumps over the lazy dog', 7));
    }

    public function testLimitChars(): void
    {
        is('The quick brown fox jump...', Str::limitChars('The quick brown fox jumps over the lazy dog', 24));
        is(
            'The quick brown fox jumps over the lazy dog',
            Str::limitChars('The quick brown fox jumps over the lazy dog', 55),
        );
        is('Th...', Str::limitChars('The quick brown fox jumps over the lazy dog', 2));
        is('The...', Str::limitChars('The quick brown fox jumps over the lazy dog', 3));
        is('The qui...', Str::limitChars('The quick brown fox jumps over the lazy dog', 7));
        is(
            'The quick brown fox jumps over the lazy dog',
            Str::limitChars('The quick brown fox jumps over the lazy dog', 150),
        );
    }

    public function testLimitWords(): void
    {
        is('The quick brown...', Str::limitWords('The quick brown fox jumps over the lazy dog', 3));
        is('The quick brown fox jumps...', Str::limitWords('The quick brown fox jumps over the lazy dog', 5));
        is('The...', Str::limitWords('The quick brown fox jumps over the lazy dog', 1));
        is(
            'The quick brown fox jumps over the lazy dog',
            Str::limitWords('The quick brown fox jumps over the lazy dog', 90),
        );
        is('The quick brown fox jumps over the...', Str::limitWords('The quick brown fox jumps over the lazy dog', 7));
    }

    public function testLike(): void
    {
        isTrue(Str::like('a', 'a'));
        isTrue(Str::like('test/*', 'test/first/second'));
        isTrue(Str::like('*/test', 'first/second/test'));
        isTrue(Str::like('test', 'TEST', false));

        isFalse(Str::like('a', ' a'));
        isFalse(Str::like('first/', 'first/second/test'));
        isFalse(Str::like('test', 'TEST'));
        isFalse(Str::like('/', '/something'));
    }

    public function testSlug(): void
    {
        is('a-simple-title', Slug::filter(' A simple     title '));
        is('this-post-it-has-a-dash', Slug::filter('This post -- it has a dash'));
        is('123-1251251', Slug::filter('123----1251251'));

        is('a-simple-title', Slug::filter('A simple title', '-'));
        is('this-post-it-has-a-dash', Slug::filter('This post -- it has a dash', '-'));
        is('123-1251251', Slug::filter('123----1251251', '-'));

        is('a_simple_title', Slug::filter('A simple title', '_'));
        is('this_post_it_has_a_dash', Slug::filter('This post -- it has a dash', '_'));
        is('123_1251251', Slug::filter('123----1251251', '_'));

        // Blank separator tests
        is('asimpletitle', Slug::filter('A simple title', ''));
        is('thispostithasadash', Slug::filter('This post -- it has a dash', ''));
        is('1231251251', Slug::filter('123----1251251', ''));

        // Css mode
        is('one-23-1251251', Slug::filter('123----1251251', '-', true));
        is('one-23-1251251', Slug::filter('123----1251251', '-', true));
        is('one_23_1251251', Slug::filter('123----1251251', '_', true));
        is('one231251251', Slug::filter('123----1251251', '', true));
        is('zero-1234567890-qwerty', Slug::filter('01234567890_qwerty', '-', true));
        is('one-234567890-qwerty', Slug::filter('1234567890_qwerty', '-', true));
        is('two-34567890-qwerty', Slug::filter('234567890_qwerty', '-', true));
        is('three-4567890-qwerty', Slug::filter('34567890_qwerty', '-', true));
        is('four-567890-qwerty', Slug::filter('4567890_qwerty', '-', true));
        is('five-67890-qwerty', Slug::filter('567890_qwerty', '-', true));
        is('six-7890-qwerty', Slug::filter('67890_qwerty', '-', true));
        is('seven-890-qwerty', Slug::filter('7890_qwerty', '-', true));
        is('eight-90-qwerty', Slug::filter('890_qwerty', '-', true));
        is('nine-0-qwerty', Slug::filter('90_qwerty', '-', true));
    }

    public function testMbString(): void
    {
        isSame(Str::isMBString(), \function_exists('mb_strtoupper'));

        is(5, Str::len('Денис'));

        isSame(0, Str::pos('Денис', 'Д'));
        isSame(1, Str::pos('Денис', 'е'));
        isSame(null, Str::pos('Денис', 'Е'));
        isSame(3, Str::rPos('Денис', 'и'));
        isSame(1, Str::iPos('Денис', 'Е'));
        isSame(1, Str::iPos('Денис', 'Е'));

        isSame('енис', Str::strStr('Денис', 'е'));
        isSame('енис', Str::iStr('Денис', 'Е'));

        isSame('ис', Str::rChr('Денис', 'и'));

        isSame('нис', Str::sub('Денис', 2));
        isSame('ени', Str::sub('Денис', 1, 3));

        isSame('денис', Str::low('ДЕНИС'));
        isSame('ДЕНИС', Str::up('денис'));

        isSame(2, Str::subCount('денис ДеНИС', 'е'));
        isSame(1, Str::subCount('денис ДеНИС', 'И'));

        isTrue(Str::isStart('денис', 'ден', true));
        isTrue(Str::isStart('денис', 'ДЕН', false));
        isFalse(Str::isStart('денис', 'ДЕН', true));

        isTrue(Str::isEnd('денис', 'нис', true));
        isTrue(Str::isEnd('денис', 'НИС', false));
        isFalse(Str::isEnd('денис', 'ДЕНИС', true));
    }

    public function testEsc(): void
    {
        isSame(
            '&lt;a href="/test"&gt;Test !@#$%^&amp;*()_+\/&lt;/a&gt;',
            Str::esc('<a href="/test">Test !@#$%^&*()_+\\/</a>'),
        );
    }

    public function testEscXML(): void
    {
        isSame(
            '&lt;a href=&quot;/test&quot;&gt;Test!@#$%^&amp;*()_+\/&lt;/a&gt;',
            Str::escXml('<a href="/test">Test!@#$%^&*()_+\\/</a>'),
        );
    }

    public function testSplitCamelCase(): void
    {
        isSame('_', Str::splitCamelCase('_'));
        isSame('word', Str::splitCamelCase('word'));
        isSame('word_and_word', Str::splitCamelCase('wordAndWord'));
        isSame('word_123_number', Str::splitCamelCase('word123Number'));
        isSame('word number', Str::splitCamelCase('wordNumber', ' '));
        isSame('word Number', Str::splitCamelCase('wordNumber', ' ', false));
        isSame('word_Number', Str::splitCamelCase('wordNumber', '_', false));
        isSame('Word_Number', Str::splitCamelCase('WordNumber', '_', false));
    }

    public function testTestName2Human(): void
    {
//        isSame('test', Str::testName2Human('test'));
//        isSame('testTest', Str::testName2Human('testTest'));
//        isSame('test_Test', Str::testName2Human('test_Test'));
//        isSame('test_test', Str::testName2Human('test_test'));
//        isSame('test test', Str::testName2Human('test test'));
//        isSame('test Test', Str::testName2Human('test Test'));
//
        isSame('Function', Str::testName2Human('testFunctionTest'));
        isSame('Function', Str::testName2Human('testFunction_Test'));
        isSame('Function', Str::testName2Human('Function_Test'));

        isSame('Function Trim', Str::testName2Human('FunctionTrim_Test'));
        isSame('Function Trim', Str::testName2Human('Function_Trim_Test'));
        isSame('Function Trim', Str::testName2Human('Function_ Trim _Test'));
        isSame('Function Trim', Str::testName2Human('Function _ Trim_ Test'));
        isSame('Function Trim', Str::testName2Human('Function _ trim_ Test'));
        isSame('Function Trim', Str::testName2Human('Function _Trim_ Test'));
        isSame('Function Trim', Str::testName2Human('Function_trim_Test'));
        isSame('Function Trim', Str::testName2Human('Function _trim_ Test'));
        isSame('Function Trim', Str::testName2Human('Function_ trim _Test'));
        isSame('Function Trim', Str::testName2Human('Function _ trim _ Test'));
        isSame('Function Trim', Str::testName2Human('testFunction _ trim _ Test'));
        isSame('Function Trim', Str::testName2Human('testfunction _ trim _ Test'));
        isSame('Function Trim', Str::testName2Human('TestFunction _ trim _ Test'));
        isSame('Function Trim', Str::testName2Human('Test_Function _ trim _ Test'));
        isSame('Function Trim', Str::testName2Human('Test_ Function _ trim _ Test'));
        isSame('Function Trim', Str::testName2Human('Test _ Function _ trim _ Test'));
        isSame('Function Trim', Str::testName2Human('Test_ Function _ trim _ Test'));
        isSame('Function Test', Str::testName2Human('Test_Function_test_Test'));
        isSame('Function Test', Str::testName2Human('Test_Function_Test_Test'));
        isSame('Function JQuery', Str::testName2Human('Test_FunctionJQuery_Test'));
        isSame('Function IE', Str::testName2Human('Test_FunctionIE_Test'));
        isSame('Function IE Test', Str::testName2Human('Test_FunctionIE_TestTest'));
        isSame('Test Function IE Test', Str::testName2Human('Test_testFunctionIE_TestTest'));

        isSame('Function IE', Str::testName2Human('\\JBZoo\\Test_FunctionIE_Test'));
        isSame('Function IE', Str::testName2Human('\\JBZoo\\PHPHunit\\Test_FunctionIE_Test'));
        isSame('Function IE', Str::testName2Human('\\JBZoo\\PHPHunit\\Some\\Test_FunctionIE_Test'));
        isSame('Function IE', Str::testName2Human('\\JBZoo\\PHPHunit\\Some\\Some\\Test_FunctionIE_Test'));
    }

    public function testGenerateUUID(): void
    {
        isNotSame(Str::uuid(), Str::uuid());
        isNotSame(Str::uuid(), Str::uuid());
        isNotSame(Str::uuid(), Str::uuid());
    }

    public function testGetClassName(): void
    {
        isSame(null, Str::getClassName(null));
        isSame('JBZoo', Str::getClassName('JBZoo'));
        isSame('JBZoo', Str::getClassName('\JBZoo'));
        isSame('CCK', Str::getClassName('\JBZoo\CCK'));
        isSame('Element', Str::getClassName('\JBZoo\CCK\Element'));
        isSame('Repeatable', Str::getClassName('\JBZoo\CCK\Element\Repeatable'));
        isSame('StringTest', Str::getClassName($this));

        isSame('StringTest', Str::getClassName($this, false));
        isSame('StringTest', Str::getClassName($this, false));
        isSame('phpunit', Str::getClassName(__NAMESPACE__, true));
    }

    public function testInc(): void
    {
        isSame('title (2)', Str::inc('title', 'default', 0));
        isSame('title(3)', Str::inc('title(2)', 'default', 0));
        isSame('title-2', Str::inc('title', 'dash', 0));
        isSame('title-3', Str::inc('title-2', 'dash', 0));
        isSame('title (4)', Str::inc('title', 'default', 4));
        isSame('title (2)', Str::inc('title', 'foo', 0));
    }

    /**
     * @noinspection SqlNoDataSourceInspection
     */
    public function testSplitSql(): void
    {
        $queries = Str::splitSql('SELECT * FROM #__foo;SELECT * FROM #__bar;');

        isSame([
            'SELECT * FROM #__foo;',
            'SELECT * FROM #__bar;',
        ], $queries);

        $queries = Str::splitSql(
            '
            ALTER TABLE `#__redirect_links` DROP INDEX `idx_link_old`;
            -- Some comment
            ALTER TABLE `#__redirect_links` MODIFY `old_url` VARCHAR(2048) NOT NULL;
            -- Some comment
            -- Some comment --
            ALTER TABLE `#__redirect_links` MODIFY `new_url` VARCHAR(2048) NOT NULL;
            -- Some comment
            ALTER TABLE `#__redirect_links` MODIFY `referer` VARCHAR(2048) NOT NULL;
            
            ALTER TABLE `#__redirect_links` ADD INDEX `idx_old_url` (`old_url`(100));
        ',
        );

        isSame([
            'ALTER TABLE `#__redirect_links` DROP INDEX `idx_link_old`;',
            'ALTER TABLE `#__redirect_links` MODIFY `old_url` VARCHAR(2048) NOT NULL;',
            'ALTER TABLE `#__redirect_links` MODIFY `new_url` VARCHAR(2048) NOT NULL;',
            'ALTER TABLE `#__redirect_links` MODIFY `referer` VARCHAR(2048) NOT NULL;',
            'ALTER TABLE `#__redirect_links` ADD INDEX `idx_old_url` (`old_url`(100));',
        ], $queries);
    }

    public function testIsStrEmpty(): void
    {
        isTrue(isStrEmpty(''));
        isTrue(isStrEmpty(false));
        isTrue(isStrEmpty("\n"));
        isTrue(isStrEmpty(" \n"));
        isTrue(isStrEmpty(' '));
        isTrue(isStrEmpty(null));

        isFalse(isStrEmpty(true));
        isFalse(isStrEmpty('0'));
        isFalse(isStrEmpty(' 0 '));
        isFalse(isStrEmpty('123'));
        isFalse(isStrEmpty('00'));
        isFalse(isStrEmpty('qwerty'));
    }
}
