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

use JBZoo\Utils\Arr;
use JBZoo\Utils\Vars;

/**
 * Class ArrayTest
 *
 * @package JBZoo\PHPUnit
 */
class ArrayTest extends PHPUnit
{

    public function testUnique()
    {
        $array = [10, 100, 1231, 10, 600, 20, 40, 1231, 20, 6, 1];
        isSame([10, 100, 1231, 600, 20, 40, 6, 1], Arr::unique($array));

        $array = ['hello', 'world', 'this', 'is', 'a', 'test', 'hello', 'is', 'a', 'word'];
        /** @noinspection ArgumentEqualsDefaultValueInspection */
        isSame(['hello', 'world', 'this', 'is', 'a', 'test', 'word'], Arr::unique($array, false));

        $array = [
            'asd_1' => 'asd',
            'asd_2' => 'asd',
        ];
        isSame(['asd_1' => 'asd'], Arr::unique($array, true));
    }

    public function testGet()
    {
        $array = [];

        $array['abc'] = 'def';
        $array['nested'] = ['key1' => 'val1', 'key2' => 'val2', 'key3' => 'val3'];

        // Looks for $array['abc']
        /** @noinspection PhpParamsInspection */
        is('def', Vars::get($array['abc']));

        // Looks for $array['nested']['key2']
        is('val2', Vars::get($array['nested']['key2']));

        // Looks for $array['not-exist']
        is('default', Vars::get($array['not-exist'], 'default'));
    }

    public function testFirst()
    {
        $test = ['a' => ['a', 'b', 'c']];
        is('a', Arr::first(Vars::get($test['a'])));
    }

    public function testFirstKey()
    {
        $test = ['a' => ['a' => 'b', 'c' => 'd']];
        is('a', Arr::firstKey(Vars::get($test['a'])));
    }

    public function testLast()
    {
        $test = ['a' => ['a', 'b', 'c']];
        is('c', Arr::last(Vars::get($test['a'])));
    }

    public function testLastKey()
    {
        $test = ['a' => ['a' => 'b', 'c' => 'd']];
        is('c', Arr::lastKey(Vars::get($test['a'])));
    }

    public function testFlatten()
    {
        $input = [
            'a',
            'b',
            'c',
            'd',
            [
                'first'  => 'e',
                'f',
                'second' => 'g',
                [
                    'h',
                    'third' => 'i',
                    [[[['j', 'k', 'l']]]],
                ],
            ],
        ];

        $expectNoKeys = range('a', 'l');
        $expectWithKeys = [
            'a',
            'b',
            'c',
            'd',
            'first'  => 'e',
            'f',
            'second' => 'g',
            'h',
            'third'  => 'i',
            'j',
            'k',
            'l',
        ];

        is($expectWithKeys, Arr::flat($input));
        is($expectNoKeys, Arr::flat($input, false));
        /** @noinspection ArgumentEqualsDefaultValueInspection */
        is($expectWithKeys, Arr::flat($input, true));
    }

    public function testSearch()
    {
        $users = [
            1 => (object)['username' => 'brandon', 'age' => 20],
            2 => (object)['username' => 'matt', 'age' => 27],
            3 => (object)['username' => 'jane', 'age' => 53],
            4 => (object)['username' => 'john', 'age' => 41],
            5 => (object)['username' => 'steve', 'age' => 11],
            6 => (object)['username' => 'fred', 'age' => 42],
            7 => (object)['username' => 'rasmus', 'age' => 21],
            8 => (object)['username' => 'don', 'age' => 15],
            9 => ['username' => 'darcy', 'age' => 33],
        ];

        $test = [
            1 => 'brandon',
            2 => 'devon',
            3 => ['troy'],
            4 => 'annie',
        ];

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
        $input = [
            '<',
            'abc',
            '>',
            'def',
            ['&', 'test', '123'],
            (object)['hey', '<>'],
        ];

        $expect = [
            '&lt;',
            'abc',
            '&gt;',
            'def',
            ['&amp;', 'test', '123'],
            (object)['hey', '<>'],
        ];

        is($expect, Arr::mapDeep($input, 'htmlentities'));
    }

    public function testClean()
    {
        $input = ['a', 'b', '', null, false, 0];
        $expect = ['a', 'b'];
        isSame($expect, Arr::clean($input));
    }

    public function testIsAssoc()
    {
        isFalse(Arr::isAssoc(['a', 'b', 'c']));
        isFalse(Arr::isAssoc(['0' => 'a', '1' => 'b', '2' => 'c']));

        isTrue(Arr::isAssoc(['1' => 'a', '0' => 'b', '2' => 'c']));
        isTrue(Arr::isAssoc(['a' => 'a', 'b' => 'b', 'c' => 'c']));
    }

    public function testUnshiftAssoc()
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        Arr::unshiftAssoc($array, 'new', 0);
        isSame($array, ['new' => 0, 'a' => 1, 'b' => 2, 'c' => 3]);

        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $newArray = Arr::unshiftAssoc($array, 'new', 42);
        isSame($newArray, ['new' => 42, 'a' => 1, 'b' => 2, 'c' => 3]);
    }

    public function testGetField()
    {
        $array = [
            ['name' => 'Bob', 'age' => 37],
            ['name' => 'Fred', 'age' => 37],
            ['name' => 'Jane', 'age' => 29],
            ['name' => 'Brandon', 'age' => 20],
            ['age' => 41],
        ];
        isSame([37, 37, 29, 20, 41], Arr::getField($array, 'age'));

        $array = [
            (object)['name' => 'Bob', 'age' => 37],
            (object)['name' => 'Fred', 'age' => 37],
            (object)['name' => 'Jane', 'age' => 29],
            (object)['name' => 'Brandon', 'age' => 20],
            (object)['age' => 41],
        ];
        isSame(['Bob', 'Fred', 'Jane', 'Brandon'], Arr::getField($array, 'name'));
    }

    public function testGroupByKey()
    {
        $array = [
            ['name' => 'Bob', 'age' => 37],
            ['name' => 'Bob', 'age' => 66],
            ['name' => 'Fred', 'age' => 20],
            ['age' => 41],
        ];
        isSame([
            'Bob'  => [
                ['name' => 'Bob', 'age' => 37],
                ['name' => 'Bob', 'age' => 66],
            ],
            'Fred' => [
                ['name' => 'Fred', 'age' => 20],
            ],
        ], Arr::groupByKey($array, 'name'));


        $array = [
            (object)['name' => 'Bob', 'age' => 37],
            (object)['name' => 'Bob', 'age' => 66],
            (object)['name' => 'Fred', 'age' => 20],
            (object)['age' => 41],
        ];

        is([
            'Bob'  => [
                (object)['name' => 'Bob', 'age' => 37],
                (object)['name' => 'Bob', 'age' => 66],
            ],
            'Fred' => [
                (object)['name' => 'Fred', 'age' => 20],
            ],
        ], Arr::groupByKey($array, 'name'));
    }

    public function testMapRecursive()
    {
        $array = [1, 2, 3, 4, 5];
        $result = Arr::map(function ($number) {
            return ($number * $number);
        }, $array);

        is([1, 4, 9, 16, 25], $result);

        $array = [1, 2, 3, 4, 5, [6, 7, [8, [[[9]]]]]];
        $result = Arr::map(function ($number) {
            return ($number * $number);
        }, $array);

        is([1, 4, 9, 16, 25, [36, 49, [64, [[[81]]]]]], $result);
    }

    public function testSortByArray()
    {
        $array = [
            'address'   => '1',
            'name'      => '2',
            'dob'       => '3',
            'no_sort_1' => '4',
            'no_sort_2' => '5',
        ];

        is([
            'dob'       => '3',
            'name'      => '2',
            'address'   => '1',
            'no_sort_1' => '4',
            'no_sort_2' => '5',
        ], Arr::sortByArray($array, ['dob', 'name', 'address']));
    }

    public function testAddEachKey()
    {
        $array = [1, 2, 3, 4, 5];
        isSame([
            'prefix_0' => 1,
            'prefix_1' => 2,
            'prefix_2' => 3,
            'prefix_3' => 4,
            'prefix_4' => 5,
        ], Arr::addEachKey($array, 'prefix_'));

        $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
        isSame([
            'prefix_a' => 1,
            'prefix_b' => 2,
            'prefix_c' => 3,
            'prefix_d' => 4,
            'prefix_e' => 5,
        ], Arr::addEachKey($array, 'prefix_'));
    }

    public function testToComment()
    {
        $array = [
            'Name' => 'Denis  ',
            'Date' => 2015,
        ];

        is('Name: Denis  ;' . PHP_EOL . 'Date: 2015;', Arr::toComment($array));
    }

    public function testCleanBeforeJson()
    {
        $array = [
            'str_empty' => '',
            'str_0'     => '0',
            'str_1'     => '1',
            'null'      => null,
            'bool'      => false,
            'num'       => 1,
            'zero'      => 0,
            'array'     => [
                'str_empty' => '',
                'str_0'     => '0',
                'str_1'     => '1',
                'null'      => null,
                'bool'      => false,
                'num'       => 1,
                'zero'      => 0,
            ],
        ];

        isSame([
            'str_0' => '0',
            'str_1' => '1',
            'bool'  => false,
            'num'   => 1,
            'zero'  => 0,
            'array' => [
                'str_0' => '0',
                'str_1' => '1',
                'bool'  => false,
                'num'   => 1,
                'zero'  => 0,
            ],
        ], Arr::cleanBeforeJson($array));
    }

    public function testIsAttr()
    {
        $array = [
            'key'   => 'asd',
            'null'  => null,
            'false' => false,
        ];

        isTrue(Arr::key('key', $array));
        isTrue(Arr::key('null', $array));
        isTrue(Arr::key('false', $array));

        isSame('asd', Arr::key('key', $array, true));
        isSame(null, Arr::key('undefined', $array, true));
        /** @noinspection ArgumentEqualsDefaultValueInspection */
        isSame(true, Arr::key('key', $array, false));
        /** @noinspection ArgumentEqualsDefaultValueInspection */
        isSame(false, Arr::key('undefined', $array, false));

        isFalse(Arr::key('undefined', $array));
        isFalse(Arr::key('', $array));
        isFalse(Arr::key(null, $array));
        isFalse(Arr::key(false, $array));
    }

    public function testIn()
    {
        $array = [
            'key'         => 'asd',
            'null'        => null,
            'some-bool'   => false,
            'some-string' => '1234567890098765432111111',
            'some-int'    => 1111112345678900987654321,
        ];

        isFalse(Arr::in(0, $array));
        isTrue(Arr::in(false, $array));

        /** @noinspection ArgumentEqualsDefaultValueInspection */
        isFalse(Arr::in(0, $array, false));
        /** @noinspection ArgumentEqualsDefaultValueInspection */
        isTrue(Arr::in(false, $array, false));

        isSame('some-string', Arr::in('1234567890098765432111111', $array, true));
        isTrue('some-int', Arr::in(1111112345678900987654321, $array, true));
        isTrue('some-bool', Arr::in(false, $array, true));
    }

    public function testWrap()
    {
        is([], Arr::wrap(null));
        is([1, 2, 3], Arr::wrap([1, 2, 3]));
        is([0], Arr::wrap(0));
        is([['key' => 'value']], Arr::wrap(['key' => 'value']));
    }

    public function testImplodeNested()
    {
        isSame('1,2,3', Arr::implode(',', [1, 2, 3]));
        isSame('123', Arr::implode('', [1, 2, 3]));
        isSame('1,2,3,4,5,6', Arr::implode(',', [1, 2, 3, [4, 5, 6]]));
        isSame('123456', Arr::implode('', [1, 2, 3, [4, 5, 6]]));

        isSame(
            '1|||||||2|||||||3|||||||4|||||||5|||||||6|||||||7|||||||8|||||||9',
            Arr::implode('|||||||', [1, 2, 3, [4, 5, 6, [7, 8, 9]]])
        );

        isSame('1,2,3', Arr::implode(',', ['key1' => 1, 'key2' => 2, 'key3' => 3]));
    }
}
