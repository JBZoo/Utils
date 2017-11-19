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
            ['', null, ''],

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
}
