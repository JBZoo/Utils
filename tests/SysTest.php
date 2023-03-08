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

use JBZoo\Utils\Sys;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class SysTest extends PHPUnit
{
    public function testIsFunc(): void
    {
        isFalse(Sys::isFunc('qwerty'));
        isTrue(Sys::isFunc('trim'));

        $func = static function (): void {
        };

        isTrue(Sys::isFunc($func));
    }

    public function testSetTimeAndMemory(): void
    {
        Sys::setTime(1800);
        Sys::setMemory('128M');

        isSame('128M', Sys::iniGet('memory_limit'));
        // isSame('1800', Sys::iniGet('set_time_limit'));
        // isSame('1800', Sys::iniGet('max_execution_time'));
    }

    public function testGetUserName(): void
    {
        isNotEmpty(Sys::getUserName());
    }

    public function testIsPHPVersion(): void
    {
        isFalse(Sys::isPHP('5.3', '4'));
        isFalse(Sys::isPHP('5.3', '4.0'));
        isFalse(Sys::isPHP('5.3', '5'));
        isFalse(Sys::isPHP('5.3', '5.0'));
        isFalse(Sys::isPHP('5.3', '5.2'));

        isTrue(Sys::isPHP('5.3.', '5.3'));
        isTrue(Sys::isPHP('5.3', '5.3'));
        isTrue(Sys::isPHP('5.3', '5.3.0'));
        isTrue(Sys::isPHP('5.3', '5.3.1'));
        isTrue(Sys::isPHP('5.3', '5.3.17'));

        isFalse(Sys::isPHP('5.3', '5.4'));
        isFalse(Sys::isPHP('5.3', '5.4.0'));
        isFalse(Sys::isPHP('5.3', '5.4.1'));

        isFalse(Sys::isPHP('5.3', '5.5'));
        isFalse(Sys::isPHP('5.3', '5.5.0'));
    }

    public function testGetMemory(): void
    {
        isTrue((bool)Sys::getMemory());
        isTrue((bool)Sys::getMemory(true));
        isTrue((bool)Sys::getMemory(false));
    }

    public function testGetDocumentRoot(): void
    {
        $_SERVER['DOCUMENT_ROOT'] = null;
        isSame(\realpath('.'), Sys::getDocRoot());

        $_SERVER['DOCUMENT_ROOT'] = __DIR__;
        isSame(__DIR__, Sys::getDocRoot());

        $_SERVER['DOCUMENT_ROOT'] = '../../';
        isSame(\realpath('../../'), Sys::getDocRoot());

        $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '\\..\\';
        isSame(PROJECT_ROOT, Sys::getDocRoot());
    }

    /**
     * @covers \JBZoo\Utils\Sys::canCollectCodeCoverage
     * @uses   \JBZoo\Utils\Sys::hasXdebug
     * @uses   \JBZoo\Utils\Sys::isHHVM
     * @uses   \JBZoo\Utils\Sys::isPHP
     */
    public function testAbilityToCollectCodeCoverageCanBeAssessed(): void
    {
        self::assertIsBool(Sys::canCollectCodeCoverage());
    }

    /**
     * @covers \JBZoo\Utils\Sys::getBinary
     * @uses   \JBZoo\Utils\Sys::isHHVM
     */
    public function testBinaryCanBeRetrieved(): void
    {
        self::assertIsString(Sys::getBinary());
    }

    /**
     * @covers \JBZoo\Utils\Sys::isHHVM
     */
    public function testCanBeDetected(): void
    {
        self::assertIsBool(Sys::isHHVM());
    }

    /**
     * @covers \JBZoo\Utils\Sys::isRealPHP
     * @uses   \JBZoo\Utils\Sys::isHHVM
     */
    public function testCanBeDetected2(): void
    {
        self::assertIsBool(Sys::isRealPHP());
    }

    /**
     * @covers \JBZoo\Utils\Sys::hasXdebug
     * @uses   \JBZoo\Utils\Sys::isHHVM
     * @uses   \JBZoo\Utils\Sys::isPHP
     */
    public function testXdebugCanBeDetected(): void
    {
        self::assertIsBool(Sys::hasXdebug());
    }

    /**
     * @covers \JBZoo\Utils\Sys::getNameWithVersion
     * @uses   \JBZoo\Utils\Sys::getName
     * @uses   \JBZoo\Utils\Sys::getVersion
     * @uses   \JBZoo\Utils\Sys::isHHVM
     * @uses   \JBZoo\Utils\Sys::isPHP
     */
    public function testNameAndVersionCanBeRetrieved(): void
    {
        self::assertIsString(Sys::getNameWithVersion());
    }

    /**
     * @covers \JBZoo\Utils\Sys::getName
     * @uses   \JBZoo\Utils\Sys::isHHVM
     */
    public function testNameCanBeRetrieved(): void
    {
        self::assertIsString(Sys::getName());
    }

    /**
     * @covers \JBZoo\Utils\Sys::getVersion
     * @uses   \JBZoo\Utils\Sys::isHHVM
     */
    public function testVersionCanBeRetrieved(): void
    {
        self::assertIsString(Sys::getVersion());
    }

    /**
     * @covers \JBZoo\Utils\Sys::getVendorUrl
     * @uses   \JBZoo\Utils\Sys::isHHVM
     */
    public function testVendorUrlCanBeRetrieved(): void
    {
        self::assertIsString(Sys::getVendorUrl());
    }
}
