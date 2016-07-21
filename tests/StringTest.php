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

use JBZoo\Utils\Str;
use JBZoo\Utils\Slug;

/**
 * Class StringTest
 * @package JBZoo\PHPUnit
 */
class StringTest extends PHPUnit
{

    public function testStrip()
    {
        $input  = ' The quick brown fox jumps over the lazy dog ';
        $expect = 'Thequickbrownfoxjumpsoverthelazydog';
        is($expect, Str::stripSpace($input));
    }

    public function testClean()
    {
        $input = ' <b>ASDF</b> !@#$%^&*()_+"\';:>< ';

        isSame('ASDF !@#$%^&*()_+"\';:><', Str::clean($input));
        isSame('asdf !@#$%^&*()_+\\"\\\';:><', Str::clean($input, true, true));
    }

    public function testParseLines()
    {
        isSame(array('asd'), Str::parseLines('asd', false));
        isSame(array('asd' => 'asd'), Str::parseLines('asd', true));
        isSame(array('asd' => 'asd'), Str::parseLines('asd'));

        $lines = array('', false, 123, 456, ' 123   ', '      ', 'ASD', '0');

        isSame(array(
            '123' => '123',
            '456' => '456',
            'ASD' => 'ASD',
            '0'   => '0',
        ), Str::parseLines(implode("\r", $lines), true));

        isSame(array(
            '123' => '123',
            '456' => '456',
            'ASD' => 'ASD',
            '0'   => '0',
        ), Str::parseLines(implode("\n", $lines), true));

        isSame(array(
            '123',
            '456',
            '123',
            'ASD',
            '0',
        ), Str::parseLines(implode("\r\n", $lines), false));
    }

    public function testHtmlentities()
    {
        is('One &amp; Two &lt;&gt; &amp;mdash;', Str::htmlEnt('One & Two <> &mdash;'));
        is('One &amp; Two &lt;&gt; &mdash;', Str::htmlEnt('One &amp; Two <> &mdash;', true));
    }

    public function testUnique()
    {
        is(15, strlen(Str::unique()));
        is(8, strlen(Str::unique(null)));
        is(10, strlen(Str::unique('t-')));
        isNotSame(Str::unique(), Str::unique());
    }

    public function testRandom()
    {
        is(10, strlen(Str::random()));
        is(10, strlen(Str::random(10)));
        is(10, strlen(Str::random(10, true)));
        is(10, strlen(Str::random(10, false)));

        isNotSame(Str::random(), Str::random());
        isNotSame(Str::random(), Str::random());
        isNotSame(Str::random(), Str::random());
    }

    public function testZeroPad()
    {
        is('341', Str::zeroPad('0341', 1));
        is('341', Str::zeroPad(341, 3));
        is('0341', Str::zeroPad(341, 4));
        is('000341', Str::zeroPad(341, 6));
    }

    public function testTruncateSafe()
    {
        is('The quick brown fox...', Str::truncateSafe('The quick brown fox jumps over the lazy dog', 24));
        is('The quick brown fox jumps over the lazy dog', Str::truncateSafe('The quick brown fox jumps over the lazy dog', 55));
        is('Th...', Str::truncateSafe('The quick brown fox jumps over the lazy dog', 2));
        is('The...', Str::truncateSafe('The quick brown fox jumps over the lazy dog', 3));
        is('The...', Str::truncateSafe('The quick brown fox jumps over the lazy dog', 7));
    }

    public function testLimitChars()
    {
        is('The quick brown fox jump...', Str::limitChars('The quick brown fox jumps over the lazy dog', 24));
        is('The quick brown fox jumps over the lazy dog', Str::limitChars('The quick brown fox jumps over the lazy dog', 55));
        is('Th...', Str::limitChars('The quick brown fox jumps over the lazy dog', 2));
        is('The...', Str::limitChars('The quick brown fox jumps over the lazy dog', 3));
        is('The qui...', Str::limitChars('The quick brown fox jumps over the lazy dog', 7));
        is('The quick brown fox jumps over the lazy dog', Str::limitChars('The quick brown fox jumps over the lazy dog', 150));
    }

    public function testLimitWords()
    {
        is('The quick brown...', Str::limitWords('The quick brown fox jumps over the lazy dog', 3));
        is('The quick brown fox jumps...', Str::limitWords('The quick brown fox jumps over the lazy dog', 5));
        is('The...', Str::limitWords('The quick brown fox jumps over the lazy dog', 1));
        is('The quick brown fox jumps over the lazy dog', Str::limitWords('The quick brown fox jumps over the lazy dog', 90));
        is('The quick brown fox jumps over the...', Str::limitWords('The quick brown fox jumps over the lazy dog', 7));
    }

    public function testLike()
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

    public function testSlug()
    {
        is('a-simple-title', Slug::filter(' A simple     title '));
        is('this-post-it-has-a-dash', Slug::filter('This post -- it has a dash'));
        is('123-1251251', Slug::filter('123----1251251'));
        is('one23-1251251', Slug::filter('123----1251251', '-', true));

        is('a-simple-title', Slug::filter('A simple title', '-'));
        is('this-post-it-has-a-dash', Slug::filter('This post -- it has a dash', '-'));
        is('123-1251251', Slug::filter('123----1251251', '-'));
        is('one23-1251251', Slug::filter('123----1251251', '-', true));

        is('a_simple_title', Slug::filter('A simple title', '_'));
        is('this_post_it_has_a_dash', Slug::filter('This post -- it has a dash', '_'));
        is('123_1251251', Slug::filter('123----1251251', '_'));
        is('one23_1251251', Slug::filter('123----1251251', '_', true));

        // Blank seperator tests
        is('asimpletitle', Slug::filter('A simple title', ''));
        is('thispostithasadash', Slug::filter('This post -- it has a dash', ''));
        is('1231251251', Slug::filter('123----1251251', ''));
        is('one231251251', Slug::filter('123----1251251', '', true));
    }

    public function testMBString()
    {
        isSame(Str::isMBString(), function_exists('mb_strtoupper'));
        isSame(Str::isOverload(), ((int)ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING));

        is(5, Str::len('Денис'));

        isSame(1, Str::pos('Денис', 'е'));
        isSame(false, Str::pos('Денис', 'Е'));
        isSame(3, Str::rpos('Денис', 'и'));
        isSame(1, Str::ipos('Денис', 'Е'));
        isSame(1, Str::ipos('Денис', 'Е'));

        isSame('енис', Str::strstr('Денис', 'е'));
        isSame('енис', Str::istr('Денис', 'Е'));

        isSame('ис', Str::rchr('Денис', 'и'));

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

    public function testEsc()
    {
        isSame(
            '&lt;a href="/test"&gt;Test !@#$%^&amp;*()_+\/&lt;/a&gt;',
            Str::esc('<a href="/test">Test !@#$%^&*()_+\\/</a>')
        );
    }

    public function testEscXML()
    {
        isSame(
            '&lt;a href=&quot;/test&quot;&gt;Test!@#$%^&amp;*()_+\/&lt;/a&gt;',
            Str::escXml('<a href="/test">Test!@#$%^&*()_+\\/</a>')
        );
    }

    public function testSplitCamelCase()
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

    public function testTestName2Human()
    {
        isSame('test', Str::testName2Human('test'));
        isSame('testTest', Str::testName2Human('testTest'));
        isSame('test_Test', Str::testName2Human('test_Test'));
        isSame('test_test', Str::testName2Human('test_test'));
        isSame('test test', Str::testName2Human('test test'));
        isSame('test Test', Str::testName2Human('test Test'));

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

    public function testGenerateUUID()
    {
        isNotSame(Str::uuid(), Str::uuid());
        isNotSame(Str::uuid(), Str::uuid());
        isNotSame(Str::uuid(), Str::uuid());
    }

    public function testGetClassName()
    {
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

    public function testInc()
    {
        isSame('title (2)', Str::inc('title', null, 0));
        isSame('title(3)', Str::inc('title(2)', null, 0));
        isSame('title-2', Str::inc('title', 'dash', 0));
        isSame('title-3', Str::inc('title-2', 'dash', 0));
        isSame('title (4)', Str::inc('title', null, 4));
        isSame('title (2)', Str::inc('title', 'foo', 0));
    }

    public function test()
    {
        $queries = Str::splitSql('SELECT * FROM #__foo;SELECT * FROM #__bar;');

        isSame(array(
            'SELECT * FROM #__foo;',
            'SELECT * FROM #__bar;'
        ), $queries);

        $queries = Str::splitSql('
            ALTER TABLE `#__redirect_links` DROP INDEX `idx_link_old`;
            -- Some comment
            ALTER TABLE `#__redirect_links` MODIFY `old_url` VARCHAR(2048) NOT NULL;
            -- Some comment
            -- Some comment --
            ALTER TABLE `#__redirect_links` MODIFY `new_url` VARCHAR(2048) NOT NULL;
            -- Some comment
            ALTER TABLE `#__redirect_links` MODIFY `referer` VARCHAR(2048) NOT NULL;
            
            ALTER TABLE `#__redirect_links` ADD INDEX `idx_old_url` (`old_url`(100));
        ');

        isSame(array(
            'ALTER TABLE `#__redirect_links` DROP INDEX `idx_link_old`;',
            'ALTER TABLE `#__redirect_links` MODIFY `old_url` VARCHAR(2048) NOT NULL;',
            'ALTER TABLE `#__redirect_links` MODIFY `new_url` VARCHAR(2048) NOT NULL;',
            'ALTER TABLE `#__redirect_links` MODIFY `referer` VARCHAR(2048) NOT NULL;',
            'ALTER TABLE `#__redirect_links` ADD INDEX `idx_old_url` (`old_url`(100));'
        ), $queries);
    }
}
