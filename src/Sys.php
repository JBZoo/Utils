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
 * Class Sys
 * @package JBZoo\Utils
 */
class Sys
{
    /**
     * Check is current OS Windows
     *
     * @return bool
     */
    public static function isWin()
    {
        return strncasecmp(PHP_OS, 'WIN', 3) === 0;
    }

    /**
     * Check is current user ROOT
     *
     * @return bool
     */
    public static function isRoot()
    {
        if (self::isFunc('posix_geteuid')) {
            return posix_geteuid() === 0;
        }

        return false; // @codeCoverageIgnore
    }

    /**
     * Returns a home directory of current user.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getHome()
    {
        if (Arr::key('HOMEDRIVE', $_SERVER)) {
            return $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
        }

        return $_SERVER['HOME'];
    }

    /**
     * Alias fo ini_set function
     *
     * @param string $varName
     * @param string $newValue
     * @return mixed
     */
    public static function iniSet($varName, $newValue)
    {
        if (self::isFunc('ini_set')) {
            return Filter::bool(ini_set($varName, $newValue));
        }

        return null; // @codeCoverageIgnore
    }

    /**
     * Alias fo ini_get function
     *
     * @param string $varName
     * @return mixed
     */
    public static function iniGet($varName)
    {
        if (self::isFunc('ini_get')) {
            return Filter::bool(ini_get($varName));
        }

        return null; // @codeCoverageIgnore
    }

    /**
     * @param $funcName
     * @return bool
     */
    public static function isFunc($funcName)
    {
        return is_callable($funcName) || (is_string($funcName) && function_exists($funcName) && is_callable($funcName));
    }

    /**
     * Set PHP execution time limit (doesn't work in safe mode)
     *
     * @param int $newLimit
     */
    public static function setTime($newLimit = 0)
    {
        $newLimit = (int)$newLimit;

        self::iniSet('set_time_limit', $newLimit);
        self::iniSet('max_execution_time', $newLimit);
        if (self::isFunc('set_time_limit') && !ini_get('safe_mode')) {
            set_time_limit($newLimit);
        }
    }

    /**
     * Set new memory limit
     *
     * @param string $newLimit
     */
    public static function setMemory($newLimit = '256M')
    {
        self::iniSet('memory_limit', $newLimit);
    }

    /**
     * @param string $version
     * @param string $current
     * @return bool
     */
    public static function isPHP($version, $current = PHP_VERSION)
    {
        $version = trim($version, '.');
        return strpos($current, $version) === 0;
    }

    /**
     * Get usage memory
     *
     * @param bool $isPeak
     * @return string
     */
    public static function getMemory($isPeak = true)
    {
        if ($isPeak) {
            $memory = memory_get_peak_usage(false);
        } else {
            $memory = memory_get_usage(false);
        }

        $result = FS::format($memory, 2);

        return $result;
    }

    /**
     * Returns the IP address of the client.
     *
     * @param   boolean $trustProxy Whether or not to trust the proxy headers HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR.
     *                              ONLY use if your server is behind a proxy that sets these values
     * @return  string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function IP($trustProxy = false)
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
     * Return document root
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return string
     */
    public static function getDocRoot()
    {
        $result = '.';
        if ($root = Arr::key('DOCUMENT_ROOT', $_SERVER, true)) {
            $result = $root;
        }

        $result = FS::clean($result);
        $result = FS::real($result);

        if (!$result) {
            $result = FS::real('.'); // @codeCoverageIgnore
        }

        return $result;
    }
}
