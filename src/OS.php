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
class OS
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
        if (function_exists('posix_geteuid') && is_callable('posix_geteuid')) {
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
        if (function_exists('ini_set') && is_callable('ini_set')) {
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
        if (function_exists('ini_get') && is_callable('ini_get')) {
            return ini_get($varName);
        }

        return null;
    }
}
