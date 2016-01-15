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

use JBZoo\Utils\Http;

/**
 * Class HttpTest
 * @package JBZoo\PHPUnit
 */
class HttpTest extends PHPUnit
{

    public function testGetIP()
    {
        $_SERVER['REMOTE_ADDR']          = '192.168.0.1';
        $_SERVER['HTTP_CLIENT_IP']       = '192.168.0.2';
        $_SERVER['HTTP_X_REAL_IP']       = '192.168.0.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.0.4';

        is('192.168.0.1', Http::IP());
        is('192.168.0.2', Http::IP(true));

        unset($_SERVER['HTTP_CLIENT_IP']);
        is('192.168.0.3', Http::IP(true));

        unset($_SERVER['HTTP_X_REAL_IP']);
        is('192.168.0.4', Http::IP(true));

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        is('192.168.0.1', Http::IP(true));
    }

    public function testGetHeaders()
    {
        $headers = Http::getHeaders();
        isNotEmpty($headers);
    }
}
