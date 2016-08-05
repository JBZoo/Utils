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

use JBZoo\Utils\Env;

/**
 * Class EnvTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class EnvTest extends PHPUnit
{
    public function testBinaryCanBeRetrieved()
    {
        $this->assertInternalType('string', Env::getBinary());
    }

    public function testCanBeDetected()
    {
        $this->assertInternalType('boolean', Env::isHHVM());
    }

    /**
     * @covers JBZoo\Utils\Env::isPHP
     * @uses   JBZoo\Utils\Env::isHHVM
     */
    public function testCanBeDetected2()
    {
        $this->assertInternalType('boolean', Env::isPHP());
    }

    /**
     * @covers JBZoo\Utils\Env::hasXdebug
     * @uses   JBZoo\Utils\Env::isHHVM
     * @uses   JBZoo\Utils\Env::isPHP
     */
    public function testXdebugCanBeDetected()
    {
        $this->assertInternalType('boolean', Env::hasXdebug());
    }

    /**
     * @covers JBZoo\Utils\Env::getNameWithVersion
     * @uses   JBZoo\Utils\Env::getName
     * @uses   JBZoo\Utils\Env::getVersion
     * @uses   JBZoo\Utils\Env::isHHVM
     * @uses   JBZoo\Utils\Env::isPHP
     */
    public function testNameAndVersionCanBeRetrieved()
    {
        $this->assertInternalType('string', Env::getNameWithVersion());
    }

    /**
     * @covers JBZoo\Utils\Env::getName
     * @uses   JBZoo\Utils\Env::isHHVM
     */
    public function testNameCanBeRetrieved()
    {
        $this->assertInternalType('string', Env::getName());
    }

    /**
     * @covers JBZoo\Utils\Env::getVersion
     * @uses   JBZoo\Utils\Env::isHHVM
     */
    public function testVersionCanBeRetrieved()
    {
        $this->assertInternalType('string', Env::getVersion());
    }

    /**
     * @covers JBZoo\Utils\Env::getVendorUrl
     * @uses   JBZoo\Utils\Env::isHHVM
     */
    public function testVendorUrlCanBeRetrieved()
    {
        $this->assertInternalType('string', Env::getVendorUrl());
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array('', null, ''),

            array('NULL', Env::VAR_NULL, null),
            array('null', Env::VAR_NULL, null),

            array('false', Env::VAR_BOOL, false),
            array('FALSE', Env::VAR_BOOL, false),
            array(0, Env::VAR_BOOL, false),
            array('true', Env::VAR_BOOL, true),
            array('True', Env::VAR_BOOL, true),
            array(1, Env::VAR_BOOL, true),

            array('42', Env::VAR_INT, 42),
            array('FALSE', Env::VAR_INT, 0),

            array('42.42', Env::VAR_FLOAT, 42.42),
            array('42', Env::VAR_FLOAT, 42.0),
            array('FALSE', Env::VAR_FLOAT, 0.),

            array('"hello"', Env::VAR_STRING, 'hello'),
            array("'hello'", Env::VAR_STRING, 'hello'),

            array('"hello"', 0, '"hello"'),
            array("'hello'", 0, "'hello'"),
        );
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
    }
}
