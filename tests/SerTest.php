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

use JBZoo\Utils\Ser;

/**
 * Class SerTest
 * @package JBZoo\PHPUnit
 */
class SerTest extends PHPUnit
{

    public function testMaybe()
    {
        $obj        = new \stdClass();
        $obj->prop1 = 'Hello';
        $obj->prop2 = 'World';

        is('This is a string', Ser::maybe('This is a string'));
        is(5.81, Ser::maybe(5.81));
        is('a:0:{}', Ser::maybe(array()));
        is('O:8:"stdClass":2:{s:5:"prop1";s:5:"Hello";s:5:"prop2";s:5:"World";}', Ser::maybe($obj));
        is('a:4:{i:0;s:4:"test";i:1;s:4:"blah";s:5:"hello";s:5:"world";s:5:"array";O:8:"stdClass":2:{s:5:"prop1";s:5:"Hello";s:5:"prop2";s:5:"World";}}', Ser::maybe(array('test', 'blah', 'hello' => 'world', 'array' => $obj)));
    }

    public function testMaybeUn()
    {
        $obj        = new \stdClass();
        $obj->prop1 = 'Hello';
        $obj->prop2 = 'World';

        isNull(Ser::maybeUn(serialize(null)));
        isFalse(Ser::maybeUn(serialize(false)));

        is('This is a string', Ser::maybeUn('This is a string'));
        is(5.81, Ser::maybeUn(5.81));
        is(array(), Ser::maybeUn('a:0:{}'));
        is($obj, Ser::maybeUn('O:8:"stdClass":2:{s:5:"prop1";s:5:"Hello";s:5:"prop2";s:5:"World";}'));
        is(array('test', 'blah', 'hello' => 'world', 'array' => $obj), Ser::maybeUn('a:4:{i:0;s:4:"test";i:1;s:4:"blah";s:5:"hello";s:5:"world";s:5:"array";O:8:"stdClass":2:{s:5:"prop1";s:5:"Hello";s:5:"prop2";s:5:"World";}}'));

        // Test a broken serialization.
        $expectedData = array(
            'Normal',
            'High-value Char: ' . chr(231) . 'a-va?',   // High-value Char:  ça-va? [in ISO-8859-1]
        );

        $brokenSerialization = 'a:2:{i:0;s:6:"Normal";i:1;s:23:"High-value Char: ▒a-va?";}';

        $unserializedData = Ser::maybeUn($brokenSerialization);
        is($expectedData[0], $unserializedData[0], 'Did not properly fix the broken serialized data.');
        is(substr($expectedData[1], 0, 10), substr($unserializedData[1], 0, 10), 'Did not properly fix the broken serialized data.');

        // Test unfixable serialization.
        $unfixableSerialization = 'a:2:{i:0;s:6:"Normal";}';
        is($unfixableSerialization, Ser::maybeUn($unfixableSerialization), 'Somehow the [previously?] impossible happened and utilphp thinks it has unserialized an unfixable serialization.');
    }

    public function testIs()
    {
        isFalse(Ser::is(1));
        isFalse(Ser::is(null));
        isFalse(Ser::is('s:4:"test;'));
        isFalse(Ser::is('a:0:{}!'));
        isFalse(Ser::is('a:0'));
        isFalse(Ser::is('This is a string'));
        isFalse(Ser::is('a string'));
        isFalse(Ser::is('z:0;'));
        isTrue(Ser::is('N;'));
        isTrue(Ser::is('b:1;'));
        isTrue(Ser::is('a:0:{}'));
        isTrue(Ser::is('O:8:"stdClass":2:{s:5:"prop1";s:5:"Hello";s:5:"prop2";s:5:"World";}'));
    }

    public function testFix()
    {
        $expectedData = array(
            'Normal',
            'High-value Char: ' . chr(231) . 'a-va?',   // High-value Char:  ça-va? [in ISO-8859-1]
        );

        $brokenSerialization = 'a:2:{i:0;s:6:"Normal";i:1;s:23:"High-value Char: ▒a-va?";}';

        // Temporarily override error handling to ensure that this is, in fact, [still] a broken serialization.
        {
            $expectedError = array(
                'errno'  => 8,
                'errstr' => 'unserialize(): Error at offset 55 of 60 bytes',
            );

            $reportedError = array();
            set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) use (&$reportedError) {
                $reportedError = compact('errno', 'errstr');
            });

            unserialize($brokenSerialization);

            is($expectedError['errno'], $reportedError['errno']);
            // Because HHVM's unserialize() error message does not contain enough info to properly test.
            if (!defined('HHVM_VERSION')) {
                is($expectedError['errstr'], $reportedError['errstr']);
            }
            restore_error_handler();
        }

        $fixedSerialization = Ser::fix($brokenSerialization);
        $unserializedData   = unserialize($fixedSerialization);
        is($expectedData[0], $unserializedData[0], 'Did not properly fix the broken serialized data.');

        is(substr($expectedData[1], 0, 10), substr($unserializedData[1], 0, 10), 'Did not properly fix the broken serialized data.');
    }
}
