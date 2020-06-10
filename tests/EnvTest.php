<?php

/**
 * JBZoo Toolbox - Utils
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Utils
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Utils
 */

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Env;

/**
 * Class EnvTest
 *
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class EnvTest extends PHPUnit
{
    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            ['NULL', Env::VAR_NULL, null],
            ['null', Env::VAR_NULL, null],

            ['false', Env::VAR_BOOL, false],
            ['FALSE', Env::VAR_BOOL, false],
            [0, Env::VAR_BOOL, false],
            ['true', Env::VAR_BOOL, true],
            ['True', Env::VAR_BOOL, true],
            [1, Env::VAR_BOOL, true],

            ['42', Env::VAR_INT, 42],
            ['FALSE', Env::VAR_INT, 0],

            ['42.42', Env::VAR_FLOAT, 42.42],
            ['42', Env::VAR_FLOAT, 42.0],
            ['FALSE', Env::VAR_FLOAT, 0.],

            ['"hello"', Env::VAR_STRING, 'hello'],
            ["'hello'", Env::VAR_STRING, 'hello'],

            ['"hello"', 0, '"hello"'],
            ["'hello'", 0, "'hello'"],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param mixed $value
     * @param int   $options
     * @param mixed $expected
     */
    public function testConvertOptions($value, $options, $expected)
    {
        isSame($expected, Env::convert($value, $options));
    }

    public function testGet()
    {
        putenv('FOO= 123 ');
        isSame('123', Env::get('FOO'));
        isSame(null, Env::get('UNDEFINED_VAR'));
        isSame(42, Env::get('UNDEFINED_VAR', 42));
        isSame(42, Env::get('UNDEFINED_VAR', 42, Env::VAR_STRING));

        $_ENV['SOME_VAR'] = '123';
        isSame('123', Env::get('SOME_VAR', 42, Env::VAR_STRING));
    }

    public function testInt()
    {
        putenv('FOO= 123 ');
        isSame(123, Env::int('FOO'));
        isSame(0, Env::int('UNDEFINED_VAR'));
        isSame(42, Env::int('UNDEFINED_VAR', 42));

        $_ENV['SOME_VAR'] = '123';
        isSame(123, Env::int('SOME_VAR'));
    }

    public function testFloat()
    {
        $value = 1 / 3;

        putenv("FOO= {$value} ");
        isSame($value, Env::float('FOO'));
        isSame(0.0, Env::float('UNDEFINED_VAR'));
        isSame(42.0, Env::float('UNDEFINED_VAR', 42));

        $_ENV['SOME_VAR'] = '0.5';
        isSame(0.5, Env::float('SOME_VAR'));
    }

    public function testString()
    {
        $value = '"qwerty"';

        putenv("FOO= {$value} ");
        isSame($value, Env::string('FOO'));
        isSame('', Env::string('UNDEFINED_VAR'));
        isSame('42', Env::string('UNDEFINED_VAR', 42));

        $_ENV['SOME_VAR'] = '0.5';
        isSame('0.5', Env::string('SOME_VAR'));


        $value = '"qwerty123';

        putenv("FOO={$value}");
        isSame($value, Env::string('FOO'));
        isSame('', Env::string('UNDEFINED_VAR'));
        isSame('42', Env::string('UNDEFINED_VAR', 42));

        $_ENV['SOME_VAR'] = '0.5';
        isSame('0.5', Env::string('SOME_VAR'));
    }

    public function testIsExists()
    {
        $notEmptyValue = '"qwerty';
        $emptyValue = '';

        putenv("FOO_STRING={$notEmptyValue}");
        putenv("FOO_EMPTY={$emptyValue}");

        isTrue(Env::isExists('FOO_STRING'));
        isTrue(Env::isExists('FOO_EMPTY'));
        isFalse(Env::isExists('FOO_QWERTY'));

        $_ENV['FOO_STRING_2'] = $notEmptyValue;
        $_ENV['FOO_EMPTY_2'] = $emptyValue;

        isTrue(Env::isExists('FOO_STRING_2'));
        isTrue(Env::isExists('FOO_EMPTY_2'));
        isFalse(Env::isExists('FOO_QWERTY_2'));
    }
}
