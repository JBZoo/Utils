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

use JBZoo\Utils\Sys;

/**
 * Class SysTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class SysTest extends PHPUnit
{
    public function testIsFunc()
    {
        isFalse(Sys::isFunc('qwerty'));
        isTrue(Sys::isFunc('trim'));

        $func = function () {
        };

        isTrue(Sys::isFunc($func));
    }

    public function testSetTimeAndMemory()
    {
        Sys::setTime(1800);
        Sys::setMemory('128M');

        isSame('128M', Sys::iniGet('memory_limit'));
        //isSame('1800', Sys::iniGet('set_time_limit'));
        //isSame('1800', Sys::iniGet('max_execution_time'));
    }

    public function testIsPHPVersion()
    {
        isFalse(Sys::isPhp('5.3', '4'));
        isFalse(Sys::isPhp('5.3', '4.0'));
        isFalse(Sys::isPhp('5.3', '5'));
        isFalse(Sys::isPhp('5.3', '5.0'));
        isFalse(Sys::isPhp('5.3', '5.2'));

        isTrue(Sys::isPhp('5.3.', '5.3'));
        isTrue(Sys::isPhp('5.3', '5.3'));
        isTrue(Sys::isPhp('5.3', '5.3.0'));
        isTrue(Sys::isPhp('5.3', '5.3.1'));
        isTrue(Sys::isPhp('5.3', '5.3.17'));

        isFalse(Sys::isPhp('5.3', '5.4'));
        isFalse(Sys::isPhp('5.3', '5.4.0'));
        isFalse(Sys::isPhp('5.3', '5.4.1'));

        isFalse(Sys::isPhp('5.3', '5.5'));
        isFalse(Sys::isPhp('5.3', '5.5.0'));
    }

    public function testIsPHP53()
    {
        isTrue(Sys::isPhp53('5.3'));
        isTrue(Sys::isPhp53('5.3.0'));
        isTrue(Sys::isPhp53('5.3.1'));

        isFalse(Sys::isPhp53('5.2'));
        isFalse(Sys::isPhp53('5.2.3'));
        isFalse(Sys::isPhp53('5.4'));
        isFalse(Sys::isPhp53('7.3'));
    }

    public function testIsPHP7()
    {
        isTrue(Sys::isPhp7('7'));
        isTrue(Sys::isPhp7('7.0'));
        isTrue(Sys::isPhp7('7.0.9'));
        isTrue(Sys::isPhp7('7.1'));
        isTrue(Sys::isPhp7('7.1.0alpha1'));

        isFalse(Sys::isPhp7('5.7'));
        isFalse(Sys::isPhp7('5.3'));
    }

    public function testGetIP()
    {
        $_SERVER['REMOTE_ADDR']          = '192.168.0.1';
        $_SERVER['HTTP_CLIENT_IP']       = '192.168.0.2';
        $_SERVER['HTTP_X_REAL_IP']       = '192.168.0.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.0.4';

        is('192.168.0.1', Sys::IP());
        is('192.168.0.2', Sys::IP(true));

        unset($_SERVER['HTTP_CLIENT_IP']);
        is('192.168.0.3', Sys::IP(true));

        unset($_SERVER['HTTP_X_REAL_IP']);
        is('192.168.0.4', Sys::IP(true));

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        is('192.168.0.1', Sys::IP(true));
    }

    public function testGetMemory()
    {
        isTrue(Sys::getMemory());
        isTrue(Sys::getMemory(true));
        isTrue(Sys::getMemory(false));
    }

    public function testGetDocumentRoot()
    {
        $_SERVER['DOCUMENT_ROOT'] = null;
        isSame(realpath('.'), Sys::getDocRoot());

        $_SERVER['DOCUMENT_ROOT'] = __DIR__;
        isSame(__DIR__, Sys::getDocRoot());

        $_SERVER['DOCUMENT_ROOT'] = '../../';
        isSame(realpath('../../'), Sys::getDocRoot());

        $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '\\..\\';
        isSame(PROJECT_ROOT, Sys::getDocRoot());
    }
}
