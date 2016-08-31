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

use JBZoo\Utils\IP;
use JBZoo\Utils\Sys;

/**
 * Class ArrayTest
 * @package JBZoo\PHPUnit
 */
class IpTest extends PHPUnit
{
    public function testGetRemote()
    {
        $_SERVER['REMOTE_ADDR']          = '192.168.0.1';
        $_SERVER['HTTP_CLIENT_IP']       = '192.168.0.2';
        $_SERVER['HTTP_X_REAL_IP']       = '192.168.0.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.0.4';

        is('192.168.0.1', Sys::IP()); // Check deprecated method
        is('192.168.0.1', IP::getRemote());

        is('192.168.0.2', IP::getRemote(true));

        unset($_SERVER['HTTP_CLIENT_IP']);
        is('192.168.0.3', IP::getRemote(true));

        unset($_SERVER['HTTP_X_REAL_IP']);
        is('192.168.0.4', IP::getRemote(true));

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        is('192.168.0.1', IP::getRemote(true));
    }

    public function testCidrMatch()
    {
        isTrue(IP::v4InRange('127.0.0.1', '0.0.0.0/0'));
        isTrue(IP::v4InRange('10.2.1.100', '10.2.0.0/16'));
        isTrue(IP::v4InRange('80.140.2.2', '80.140.*.*'));
        isTrue(IP::v4InRange('80.140.2.3', '80.140/16'));
        isTrue(IP::v4InRange('1.2.3.4', '1.2.3.0-1.2.255.255'));
        isTrue(IP::v4InRange('80.76.201.37', '80.76.201.32/27'));
        isTrue(IP::v4InRange('80.76.201.38', '80.76.201.32/255.255.255.224'));
        isTrue(IP::v4InRange('80.76.201.39', '80.76.201.32/255.255.255.*'));
        isTrue(IP::v4InRange('128.0.0.0', '127.0.0.0-129.0.0.0'));

        isFalse(IP::v4InRange('1.2.3.4', '10.2.0.0/16'));
        isFalse(IP::v4InRange('80.141.2.2', '80.140.*.*'));
        isFalse(IP::v4InRange('90.35.6.12', '80.140.0.0-80.140.255.255'));
        isFalse(IP::v4InRange('81.76.201.37', '80.76.201.32/27'));
        isFalse(IP::v4InRange('80.76.201.40', '80.76.201.64/27'));
        isFalse(IP::v4InRange('192.168.1.42', '192.168.3.0/24'));
    }

    public function testGetNetMask()
    {
        is('255.0.0.0', IP::getNetMask('127.0.0.1'));
        is('255.0.0.0', IP::getNetMask('127.0.0.0'));
        is('255.0.0.0', IP::getNetMask('127.255.0.0'));
        is('255.255.0.0', IP::getNetMask('128.0.0.0'));
        is('255.255.255.0', IP::getNetMask('192.0.0.0'));
        is('255.255.255.255', IP::getNetMask('224.0.0.0'));
        is('255.255.255.255', IP::getNetMask('255.0.0.0'));
    }
}
