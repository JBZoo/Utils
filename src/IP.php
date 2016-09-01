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

namespace JBZoo\Utils;

/**
 * Class IP
 * @package JBZoo\Utils
 */
class IP
{
    /**
     * Returns the IP address of the client.
     *
     * @param   boolean $trustProxy Whether or not to trust the proxy headers HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR.
     *                              ONLY use if your server is behind a proxy that sets these values
     * @return  string
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getRemote($trustProxy = false)
    {
        if (!$trustProxy) {
            return $_SERVER['REMOTE_ADDR'];
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];

        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ipAddress = $_SERVER['HTTP_X_REAL_IP'];

        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];

        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        return $ipAddress;
    }

    /**
     * Check if a given ip is in a network
     * @param  string $ipAddress IP to check in IPV4 format eg. 127.0.0.1
     * @param  string $range     IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
     *
     * @return bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @see https://www.pgregg.com/projects/php/ip_in_range/
     */
    public static function v4InRange($ipAddress, $range)
    {
        if (strpos($range, '/') !== false) {
            // $range is in IP/NETMASK format
            list($range, $netmask) = explode('/', $range, 2);

            if (strpos($netmask, '.') !== false) {
                // $netmask is a 255.255.0.0 format
                $netmask    = str_replace('*', '0', $netmask);
                $netmaskDec = ip2long($netmask);

                return ((ip2long($ipAddress) & $netmaskDec) == (ip2long($range) & $netmaskDec));

            } else {
                // $netmask is a CIDR size block
                // fix the range argument
                $blocks = explode('.', $range);

                while (count($blocks) < 4) {
                    $blocks[] = '0';
                }

                list($blockA, $blockB, $blockC, $blockD) = $blocks;

                $range = sprintf(
                    "%u.%u.%u.%u",
                    empty($blockA) ? '0' : $blockA,
                    empty($blockB) ? '0' : $blockB,
                    empty($blockC) ? '0' : $blockC,
                    empty($blockD) ? '0' : $blockD
                );

                $rangeDec = ip2long($range);
                $ipDec    = ip2long($ipAddress);

                $wildcardDec = pow(2, (32 - $netmask)) - 1;
                $netmaskDec  = ~$wildcardDec;

                return (($ipDec & $netmaskDec) == ($rangeDec & $netmaskDec));
            }

        } else {
            // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
            if (strpos($range, '*') !== false) { // a.b.*.* format
                // Just convert to A-B format by setting * to 0 for A and 255 for B
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }

            if (strpos($range, '-') !== false) { // A-B format
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float)sprintf("%u", ip2long($lower));
                $upper_dec = (float)sprintf("%u", ip2long($upper));
                $ipDec     = (float)sprintf("%u", ip2long($ipAddress));
                return (($ipDec >= $lower_dec) && ($ipDec <= $upper_dec));
            }

            return false;
        }
    }

    /**
     * @param $ipAddress
     * @return int
     */
    public static function getNetMask($ipAddress)
    {
        if (is_string($ipAddress)) {
            $ipAddress = ip2long($ipAddress);
        }

        if (($ipAddress & 0x80000000) == 0) {
            $mask = 0xFF000000;

        } elseif (($ipAddress & 0xC0000000) == (int)0x80000000) {
            $mask = 0xFFFF0000;

        } elseif (($ipAddress & 0xE0000000) == (int)0xC0000000) {
            $mask = 0xFFFFFF00;

        } else {
            $mask = 0xFFFFFFFF;
        }

        return long2ip($mask);
    }
}
