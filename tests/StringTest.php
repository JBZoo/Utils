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

        same('ASDF !@#$%^&*()_+"\';:><', Str::clean($input));
        same('asdf !@#$%^&*()_+\\"\\\';:><', Str::clean($input, true, true));
    }

    public function testParseLines()
    {
        same(array('asd'), Str::parseLines('asd', false));
        same(array('asd' => 'asd'), Str::parseLines('asd', true));
        same(array('asd' => 'asd'), Str::parseLines('asd'));

        $lines = array('', false, 123, 456, ' 123   ', '      ', 'ASD', '0');

        same(array(
            '123' => '123',
            '456' => '456',
            'ASD' => 'ASD',
            '0'   => '0',
        ), Str::parseLines(implode("\r", $lines), true));

        same(array(
            '123' => '123',
            '456' => '456',
            'ASD' => 'ASD',
            '0'   => '0',
        ), Str::parseLines(implode("\n", $lines), true));

        same(array(
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

    public function testHtmlspecialchars()
    {
        is('One &amp; Two &lt;&gt; &amp;mdash;', Str::htmlChars('One & Two <> &mdash;'));
        is('One &amp; Two &lt;&gt; &mdash;', Str::htmlChars('One &amp; Two <> &mdash;', true));
    }

    public function testUnique()
    {
        is(13, strlen(Str::unique()));
        is(6, strlen(Str::unique(null)));
        is(8, strlen(Str::unique('t-')));
    }

    public function testRandom()
    {
        is(10, strlen(Str::random(10)));
        is(10, strlen(Str::random(10, true)));
        is(10, strlen(Str::random(10, false)));
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
        is('a-simple-title', Str::slug(' A simple     title '));
        is('this-post-it-has-a-dash', Str::slug('This post -- it has a dash'));
        is('123-1251251', Str::slug('123----1251251'));
        is('one23-1251251', Str::slug('123----1251251', '-', true));

        is('a-simple-title', Str::slug('A simple title', '-'));
        is('this-post-it-has-a-dash', Str::slug('This post -- it has a dash', '-'));
        is('123-1251251', Str::slug('123----1251251', '-'));
        is('one23-1251251', Str::slug('123----1251251', '-', true));

        is('a_simple_title', Str::slug('A simple title', '_'));
        is('this_post_it_has_a_dash', Str::slug('This post -- it has a dash', '_'));
        is('123_1251251', Str::slug('123----1251251', '_'));
        is('one23_1251251', Str::slug('123----1251251', '_', true));

        // Blank seperator tests
        is('asimpletitle', Str::slug('A simple title', ''));
        is('thispostithasadash', Str::slug('This post -- it has a dash', ''));
        is('1231251251', Str::slug('123----1251251', ''));
        is('one231251251', Str::slug('123----1251251', '', true));
    }

    public function testMBString()
    {
        same(Str::isMBString(), function_exists('mb_strtoupper'));
        same(Str::isOverload(), ((int)ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING));

        is(5, Str::len('Денис'));

        same(1, Str::pos('Денис', 'е'));
        same(false, Str::pos('Денис', 'Е'));
        same(3, Str::rpos('Денис', 'и'));
        same(1, Str::ipos('Денис', 'Е'));
        same(1, Str::ipos('Денис', 'Е'));

        same('енис', Str::str('Денис', 'е'));
        same('енис', Str::istr('Денис', 'Е'));

        same('ис', Str::rchr('Денис', 'и'));

        same('нис', Str::sub('Денис', 2));
        same('ени', Str::sub('Денис', 1, 3));

        same('денис', Str::low('ДЕНИС'));
        same('ДЕНИС', Str::up('денис'));

        same(2, Str::subCount('денис ДеНИС', 'е'));
        same(1, Str::subCount('денис ДеНИС', 'И'));

        isTrue(Str::isStart('денис', 'ден', true));
        isTrue(Str::isStart('денис', 'ДЕН', false));
        isFalse(Str::isStart('денис', 'ДЕН', true));

        isTrue(Str::isEnd('денис', 'нис', true));
        isTrue(Str::isEnd('денис', 'НИС', false));
        isFalse(Str::isEnd('денис', 'ДЕНИС', true));
    }

}
