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
            return ini_get($varName);
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
        return preg_match('#^' . preg_quote($version) . '#i', $current);
    }

    /**
     * @param string $current
     * @return bool
     */
    public static function isPHP53($current = PHP_VERSION)
    {
        return self::isPHP('5.3', $current);
    }

    /**
     * @param string $current
     * @return bool
     */
    public static function isPHP7($current = PHP_VERSION)
    {
        return self::isPHP('7', $current);
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
     * Get remote IP
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     * @deprecated use IP::getRemote()
     *
     * @param bool $trustProxy
     * @return string
     */
    public static function IP($trustProxy = false)
    {
        return IP::getRemote($trustProxy);
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
        $root   = Arr::key('DOCUMENT_ROOT', $_SERVER, true);

        if ($root) {
            $result = $root;
        }

        $result = FS::clean($result);
        $result = FS::real($result);

        if (!$result) {
            $result = FS::real('.'); // @codeCoverageIgnore
        }

        return $result;
    }

    /**
     * Returns true when Xdebug is supported or
     * the runtime used is PHPDBG (PHP >= 7.0).
     *
     * @return bool
     */
    public static function canCollectCodeCoverage()
    {
        return self::hasXdebug() || self::hasPHPDBGCodeCoverage();
    }

    /**
     * @var string
     */
    private static $_binary;

    /**
     * Returns the path to the binary of the current runtime.
     * Appends ' --php' to the path when the runtime is HHVM.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @codeCoverageIgnore
     */
    public static function getBinary()
    {
        // Custom PHP path
        if (self::$_binary === null) {
            if ((self::$_binary = getenv('PHP_BINARY_CUSTOM')) === false) {
                self::$_binary = PHP_BINARY;
            }
        }

        // HHVM
        if (self::$_binary === null && self::isHHVM()) {
            if ((self::$_binary = getenv('PHP_BINARY')) === false) {
                self::$_binary = PHP_BINARY;
            }
            self::$_binary = escapeshellarg(self::$_binary) . ' --php';
        }

        // PHP >= 5.4.0
        if (self::$_binary === null && defined('PHP_BINARY')) {
            self::$_binary = escapeshellarg(PHP_BINARY);
        }

        // PHP < 5.4.0
        if (self::$_binary === null) {
            if (PHP_SAPI == 'cli' && isset($_SERVER['_'])) {
                if (strpos($_SERVER['_'], 'phpunit') !== false) {
                    $file = file($_SERVER['_']);

                    if (strpos($file[0], ' ') !== false) {
                        $tmp           = explode(' ', $file[0]);
                        self::$_binary = escapeshellarg(trim($tmp[1]));
                    } else {
                        self::$_binary = escapeshellarg(ltrim(trim($file[0]), '#!'));
                    }

                } elseif (strpos(basename($_SERVER['_']), 'php') !== false) {
                    self::$_binary = escapeshellarg($_SERVER['_']);
                }
            }
        }

        if (self::$_binary === null) {
            $binaryLocations = array(
                PHP_BINDIR . '/php',
                PHP_BINDIR . '/php-cli.exe',
                PHP_BINDIR . '/php.exe',
            );

            foreach ($binaryLocations as $binary) {
                if (is_readable($binary)) {
                    self::$_binary = escapeshellarg($binary);
                    break;
                }
            }
        }

        if (self::$_binary === null) {
            self::$_binary = 'php';
        }

        return self::$_binary;
    }

    /**
     * @return string
     */
    public static function getNameWithVersion()
    {
        return self::getName() . ' ' . self::getVersion();
    }

    /**
     * @return string
     */
    public static function getName()
    {
        if (self::isHHVM()) {
            return 'HHVM';

        } elseif (self::isPHPDBG()) {
            return 'PHPDBG';
        }

        return 'PHP';
    }

    /**
     * @return string
     */
    public static function getVendorUrl()
    {
        if (self::isHHVM()) {
            return 'http://hhvm.com/';
        } else {
            return 'http://php.net/';
        }
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        if (self::isHHVM()) {
            return HHVM_VERSION;
        } else {
            return PHP_VERSION;
        }
    }

    /**
     * Returns true when the runtime used is PHP and Xdebug is loaded.
     *
     * @return bool
     */
    public static function hasXdebug()
    {
        return (self::isRealPHP() || self::isHHVM()) && extension_loaded('xdebug');
    }

    /**
     * Returns true when the runtime used is HHVM.
     *
     * @return bool
     */
    public static function isHHVM()
    {
        return defined('HHVM_VERSION');
    }

    /**
     * Returns true when the runtime used is PHP without the PHPDBG SAPI.
     *
     * @return bool
     */
    public static function isRealPHP()
    {
        return !self::isHHVM() && !self::isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI.
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public static function isPHPDBG()
    {
        return PHP_SAPI === 'phpdbg' && !self::isHHVM();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI
     * and the phpdbg_*_oplog() functions are available (PHP >= 7.0).
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public static function hasPHPDBGCodeCoverage()
    {
        return self::isPHPDBG() && function_exists('phpdbg_start_oplog');
    }
}
