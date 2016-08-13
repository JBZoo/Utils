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

use JBZoo\Utils\Arr;
use JBZoo\Utils\Vars;

/**
 * Class ArrayTest
 * @package JBZoo\PHPUnit
 */
class ArrayTest extends PHPUnit
{

    public function testUnique()
    {
        $array = array(10, 100, 1231, 10, 600, 20, 40, 1231, 20, 6, 1);
        isSame(array(10, 100, 1231, 600, 20, 40, 6, 1), Arr::unique($array));

        $array = array('hello', 'world', 'this', 'is', 'a', 'test', 'hello', 'is', 'a', 'word');
        isSame(array('hello', 'world', 'this', 'is', 'a', 'test', 'word'), Arr::unique($array, false));

        $array = array(
            'asd_1' => 'asd',
            'asd_2' => 'asd',
        );
        isSame(array('asd_1' => 'asd'), Arr::unique($array, true));
    }

    public function testGet()
    {
        $array = array();

        $array['abc']    = 'def';
        $array['nested'] = array('key1' => 'val1', 'key2' => 'val2', 'key3' => 'val3');

        // Looks for $array['abc']
        is('def', Vars::get($array['abc']));

        // Looks for $array['nested']['key2']
        is('val2', Vars::get($array['nested']['key2']));

        // Looks for $array['doesnotexist']
        is('defaultval', Vars::get($array['doesnotexist'], 'defaultval'));
    }

    public function testFirst()
    {
        $test = array('a' => array('a', 'b', 'c'));
        is('a', Arr::first(Vars::get($test['a'])));
    }

    public function testFirstKey()
    {
        $test = array('a' => array('a' => 'b', 'c' => 'd'));
        is('a', Arr::firstKey(Vars::get($test['a'])));
    }

    public function testLast()
    {
        $test = array('a' => array('a', 'b', 'c'));
        is('c', Arr::last(Vars::get($test['a'])));
    }

    public function testLastKey()
    {
        $test = array('a' => array('a' => 'b', 'c' => 'd'));
        is('c', Arr::lastKey(Vars::get($test['a'])));
    }

    public function testFlatten()
    {
        $input = array('a', 'b', 'c', 'd',
            array(
                'first'  => 'e',
                'f',
                'second' => 'g',
                array('h',
                    'third' => 'i',
                    array(array(array(array('j', 'k', 'l')))))));

        $expectNoKeys   = range('a', 'l');
        $expectWithKeys = array(
            'a', 'b', 'c', 'd',
            'first'  => 'e',
            'f',
            'second' => 'g',
            'h',
            'third'  => 'i',
            'j', 'k', 'l',
        );

        is($expectWithKeys, Arr::flat($input));
        is($expectNoKeys, Arr::flat($input, false));
        is($expectWithKeys, Arr::flat($input, true));
    }

    public function testSearch()
    {
        $users = array(
            1 => (object)array('username' => 'brandon', 'age' => 20),
            2 => (object)array('username' => 'matt', 'age' => 27),
            3 => (object)array('username' => 'jane', 'age' => 53),
            4 => (object)array('username' => 'john', 'age' => 41),
            5 => (object)array('username' => 'steve', 'age' => 11),
            6 => (object)array('username' => 'fred', 'age' => 42),
            7 => (object)array('username' => 'rasmus', 'age' => 21),
            8 => (object)array('username' => 'don', 'age' => 15),
            9 => array('username' => 'darcy', 'age' => 33),
        );

        $test = array(
            1 => 'brandon',
            2 => 'devon',
            3 => array('troy'),
            4 => 'annie',
        );

        isFalse(Arr::search($test, 'bob'));
        is(3, Arr::search($test, 'troy'));
        is(4, Arr::search($test, 'annie'));
        is(2, Arr::search($test, 'devon', 'devon'));
        is(7, Arr::search($users, 'rasmus', 'username'));
        is(9, Arr::search($users, 'darcy', 'username'));
        is(1, Arr::search($users, 'brandon'));
    }

    public function testMapDeep()
    {
        $input = array(
            '<',
            'abc',
            '>',
            'def',
            array('&', 'test', '123'),
            (object)array('hey', '<>'),
        );

        $expect = array(
            '&lt;',
            'abc',
            '&gt;',
            'def',
            array('&amp;', 'test', '123'),
            (object)array('hey', '<>'),
        );

        is($expect, Arr::mapDeep($input, 'htmlentities'));
    }

    public function testClean()
    {
        $input  = array('a', 'b', '', null, false, 0);
        $expect = array('a', 'b');
        isSame($expect, Arr::clean($input));
    }

    public function testIsAssoc()
    {
        isFalse(Arr::isAssoc(array('a', 'b', 'c')));
        isFalse(Arr::isAssoc(array("0" => 'a', "1" => 'b', "2" => 'c')));

        isTrue(Arr::isAssoc(array("1" => 'a', "0" => 'b', "2" => 'c')));
        isTrue(Arr::isAssoc(array("a" => 'a', "b" => 'b', "c" => 'c')));
    }

    public function testUnshiftAssoc()
    {
        $array = array('a' => 1, 'b' => 2, 'c' => 3);
        Arr::unshiftAssoc($array, 'new', 0);
        isSame($array, array('new' => 0, 'a' => 1, 'b' => 2, 'c' => 3));

        $array    = array('a' => 1, 'b' => 2, 'c' => 3);
        $newArray = Arr::unshiftAssoc($array, 'new', 42);
        isSame($newArray, array('new' => 42, 'a' => 1, 'b' => 2, 'c' => 3));
    }

    public function testGetField()
    {
        $array = array(
            array('name' => 'Bob', 'age' => 37),
            array('name' => 'Fred', 'age' => 37),
            array('name' => 'Jane', 'age' => 29),
            array('name' => 'Brandon', 'age' => 20),
            array('age' => 41),
        );
        isSame(array(37, 37, 29, 20, 41), Arr::getField($array, 'age'));

        $array = array(
            (object)array('name' => 'Bob', 'age' => 37),
            (object)array('name' => 'Fred', 'age' => 37),
            (object)array('name' => 'Jane', 'age' => 29),
            (object)array('name' => 'Brandon', 'age' => 20),
            (object)array('age' => 41),
        );
        isSame(array('Bob', 'Fred', 'Jane', 'Brandon'), Arr::getField($array, 'name'));
    }

    public function testGroupByKey()
    {
        $array = array(
            array('name' => 'Bob', 'age' => 37),
            array('name' => 'Bob', 'age' => 66),
            array('name' => 'Fred', 'age' => 20),
            array('age' => 41),
        );
        isSame(array(
            'Bob'  => array(
                array('name' => 'Bob', 'age' => 37),
                array('name' => 'Bob', 'age' => 66),
            ),
            'Fred' => array(
                array('name' => 'Fred', 'age' => 20),
            ),
        ), Arr::groupByKey($array, 'name'));


        $array = array(
            (object)array('name' => 'Bob', 'age' => 37),
            (object)array('name' => 'Bob', 'age' => 66),
            (object)array('name' => 'Fred', 'age' => 20),
            (object)array('age' => 41),
        );

        is(array(
            'Bob'  => array(
                (object)array('name' => 'Bob', 'age' => 37),
                (object)array('name' => 'Bob', 'age' => 66),
            ),
            'Fred' => array(
                (object)array('name' => 'Fred', 'age' => 20),
            ),
        ), Arr::groupByKey($array, 'name'));
    }

    public function testMapRecursive()
    {
        $array  = array(1, 2, 3, 4, 5);
        $result = Arr::map(function ($number) {
            return ($number * $number);
        }, $array);

        is(array(1, 4, 9, 16, 25), $result);

        $array  = array(1, 2, 3, 4, 5, array(6, 7, array(8, array(array(array(9))))));
        $result = Arr::map(function ($number) {
            return ($number * $number);
        }, $array);

        is(array(1, 4, 9, 16, 25, array(36, 49, array(64, array(array(array(81)))))), $result);
    }

    public function testSortByArray()
    {
        $array = array(
            'address'   => '1',
            'name'      => '2',
            'dob'       => '3',
            'no_sort_1' => '4',
            'no_sort_2' => '5',
        );

        is(array(
            'dob'       => '3',
            'name'      => '2',
            'address'   => '1',
            'no_sort_1' => '4',
            'no_sort_2' => '5',
        ), Arr::sortByArray($array, array('dob', 'name', 'address')));
    }

    public function testAddEachKey()
    {
        $array = array(1, 2, 3, 4, 5);
        isSame(array(
            "prefix_0" => 1,
            "prefix_1" => 2,
            "prefix_2" => 3,
            "prefix_3" => 4,
            "prefix_4" => 5,
        ), Arr::addEachKey($array, 'prefix_'));

        $array = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);
        isSame(array(
            "prefix_a" => 1,
            "prefix_b" => 2,
            "prefix_c" => 3,
            "prefix_d" => 4,
            "prefix_e" => 5,
        ), Arr::addEachKey($array, 'prefix_'));
    }

    public function testToComment()
    {
        $array = array(
            'Name' => 'Denis  ',
            'Date' => 2015,
        );

        is('Name: Denis  ;' . PHP_EOL . 'Date: 2015;', Arr::toComment($array));
    }

    public function testCleanBeforeJson()
    {
        $array = array(
            'str_empty' => '',
            'str_0'     => '0',
            'str_1'     => '1',
            'null'      => null,
            'bool'      => false,
            'num'       => 1,
            'zero'      => 0,
            'array'     => array(
                'str_empty' => '',
                'str_0'     => '0',
                'str_1'     => '1',
                'null'      => null,
                'bool'      => false,
                'num'       => 1,
                'zero'      => 0,
            ),
        );

        isSame(array(
            'str_0' => '0',
            'str_1' => '1',
            'bool'  => false,
            'num'   => 1,
            'zero'  => 0,
            'array' => array(
                'str_0' => '0',
                'str_1' => '1',
                'bool'  => false,
                'num'   => 1,
                'zero'  => 0,
            ),
        ), Arr::cleanBeforeJson($array));
    }

    public function testIsAttr()
    {
        $array = array(
            'key'   => 'asd',
            'null'  => null,
            'false' => false,
        );

        isTrue(Arr::key('key', $array));
        isTrue(Arr::key('null', $array));
        isTrue(Arr::key('false', $array));

        isSame('asd', Arr::key('key', $array, true));
        isSame(null, Arr::key('undefined', $array, true));
        isSame(true, Arr::key('key', $array, false));
        isSame(false, Arr::key('undefined', $array, false));

        isFalse(Arr::key('undefined', $array));
        isFalse(Arr::key('', $array));
        isFalse(Arr::key(null, $array));
        isFalse(Arr::key(false, $array));
    }

    public function testIn()
    {
        $array = array(
            'key'         => 'asd',
            'null'        => null,
            'some-bool'   => false,
            'some-string' => '1234567890098765432111111',
            'some-int'    => 1111112345678900987654321,
        );

        isFalse(Arr::in(0, $array));
        isTrue(Arr::in(false, $array));

        isFalse(Arr::in(0, $array, false));
        isTrue(Arr::in(false, $array, false));

        isSame('some-string', Arr::in('1234567890098765432111111', $array, true));
        isTrue('some-int', Arr::in(1111112345678900987654321, $array, true));
        isTrue('some-bool', Arr::in(false, $array, true));
    }

    public function testWrap()
    {
        is(array(), Arr::wrap(null));
        is(array(1, 2, 3), Arr::wrap(array(1, 2, 3)));
        is(array(0), Arr::wrap(0));
        is(array(array('key' => 'value')), Arr::wrap(array('key' => 'value')));
    }

    public function testImplodeNested()
    {
        isSame('1,2,3', Arr::implode(',', array(1, 2, 3)));
        isSame('123', Arr::implode('', array(1, 2, 3)));
        isSame('1,2,3,4,5,6', Arr::implode(',', array(1, 2, 3, array(4, 5, 6))));
        isSame('123456', Arr::implode('', array(1, 2, 3, array(4, 5, 6))));

        isSame(
            '1|||||||2|||||||3|||||||4|||||||5|||||||6|||||||7|||||||8|||||||9',
            Arr::implode('|||||||', array(1, 2, 3, array(4, 5, 6, array(7, 8, 9))))
        );

        isSame('1,2,3', Arr::implode(',', array('key1' => 1, 'key2' => 2, 'key3' => 3)));
    }
}
