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
 * Class OS
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

        return false;
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
            return @ini_set($varName, $newValue);
        }

        return null;
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
            return ini_get($varName);
        }

        return null;
    }

    /**
     * @param $funcName
     * @return bool
     */
    public static function isFunc($funcName)
    {
        return is_string($funcName) && function_exists($funcName) && is_callable($funcName);
    }

    /**
     * Set PHP execution time limit
     *
     * @param int $newLimit
     */
    public static function setTime($newLimit = -1)
    {
        $newLimit = (int)$newLimit;

        self::iniSet('set_time_limit', $newLimit);
        if (self::isFunc('set_time_limit')) {
            set_time_limit(1800);
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
}
