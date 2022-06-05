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

declare(strict_types=1);

namespace JBZoo\Utils;

/**
 * Class IP
 *
 * @package JBZoo\Utils
 * @SuppressWarnings(PHPMD.ShortClassName)
 */
final class IP
{
    /**
     * Returns the IP address of the client.
     *
     * @param bool $trustProxy Whether or not to trust the proxy headers HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR.
     *                         ONLY use if your server is behind a proxy that sets these values
     * @return  string
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getRemote(bool $trustProxy = false): string
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
     *
     * @param string $ipAddress IP to check in IPV4 format eg. 127.0.0.1
     * @param string $range     IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
     *
     * @return bool
     * @SuppressWarnings(PHPMD)
     * @see https://www.pgregg.com/projects/php/ip_in_range/
     */
    public static function v4InRange(string $ipAddress, string $range): bool
    {
        if (\strpos($range, '/') !== false) {
            // $range is in IP/NETMASK format
            [$range, $netMask] = \explode('/', $range, 2);

            if (\strpos($netMask, '.') !== false) {
                // $netMask is a 255.255.0.0 format
                $netMask = \str_replace('*', '0', $netMask);
                $netMaskDec = \ip2long($netMask);

                return ((\ip2long($ipAddress) & $netMaskDec) === (\ip2long($range) & $netMaskDec));
            }

            // $netMask is a CIDR size block
            // fix the range argument
            $blocks = \explode('.', $range, 4);

            $range = \sprintf(
                '%u.%u.%u.%u',
                int($blocks[0] ?? 0),
                int($blocks[1] ?? 0),
                int($blocks[2] ?? 0),
                int($blocks[3] ?? 0)
            );

            $rangeDec = \ip2long($range);
            $ipDec = \ip2long($ipAddress);

            $netMask = (int)$netMask;
            $wildcardDec = (2 ** (32 - $netMask)) - 1;
            $netMaskDec = ~$wildcardDec;

            return (($ipDec & $netMaskDec) === ($rangeDec & $netMaskDec));
        }

        // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
        if (\strpos($range, '*') !== false) { // a.b.*.* format
            // Just convert to A-B format by setting * to 0 for A and 255 for B
            $lower = \str_replace('*', '0', $range);
            $upper = \str_replace('*', '255', $range);
            $range = "$lower-$upper";
        }

        if (\strpos($range, '-') !== false) { // A-B format
            [$lower, $upper] = \explode('-', $range, 2);
            $lowerDec = (float)\sprintf('%u', (int)\ip2long($lower));
            $upperDec = (float)\sprintf('%u', (int)\ip2long($upper));
            $ipDec = (float)\sprintf('%u', (int)\ip2long($ipAddress));
            return (($ipDec >= $lowerDec) && ($ipDec <= $upperDec));
        }

        return false;
    }

    /**
     * Return network mask. For example, '192.0.0.0' => '255.255.255.0'
     *
     * @param string $ipAddress
     * @return string|null
     */
    public static function getNetMask(string $ipAddress): ?string
    {
        $ipAddressLong = \ip2long($ipAddress);

        $maskLevel1 = 0x80000000;
        $maskLevel2 = 0xC0000000;
        $maskLevel3 = 0xE0000000;

        $resultMask = 0xFFFFFFFF;
        if (($ipAddressLong & $maskLevel1) === 0) {
            $resultMask = 0xFF000000;
        } elseif (($ipAddressLong & $maskLevel2) === $maskLevel1) {
            $resultMask = 0xFFFF0000;
        } elseif (($ipAddressLong & $maskLevel3) === $maskLevel2) {
            $resultMask = 0xFFFFFF00;
        }

        return \long2ip($resultMask) ?: null;
    }
}
