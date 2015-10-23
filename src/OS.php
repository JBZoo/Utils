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
        return posix_geteuid() === 0;
    }

    /**
     * Returns a home directory of current user.
     *
     * @return string
     */
    public static function getHome()
    {
        if (isset($_SERVER['HOMEDRIVE'])) {
            return $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
        }

        return $_SERVER['HOME'];
    }
}
