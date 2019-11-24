<?php
/**
 * JBZoo Utils
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Utils
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Utils
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\Utils;

/**
 * Class Sys
 *
 * @package JBZoo\Utils
 */
class Sys
{
    /**
     * @var string
     */
    private static $binary;

    /**
     * Check is current OS Windows
     *
     * @return bool
     */
    public static function isWin(): bool
    {
        return strncasecmp(PHP_OS, 'WIN', 3) === 0;
    }

    /**
     * Check is current user ROOT
     *
     * @return bool
     */
    public static function isRoot(): bool
    {
        if (self::isFunc('posix_geteuid')) {
            return 0 === posix_geteuid();
        }

        return false; // @codeCoverageIgnore
    }

    /**
     * Returns current linux user who runs script
     * @return string|null
     */
    public static function getUserName(): ?string
    {
        $userInfo = posix_getpwuid(posix_geteuid());
        return $userInfo['name'] ?? null;
    }

    /**
     * Returns a home directory of current user.
     *
     * @return string|null
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getHome(): ?string
    {
        $userInfo = posix_getpwuid(posix_geteuid());
        if (isset($userInfo['dir'])) {
            return $userInfo['dir'];
        }

        if (Arr::key('HOMEDRIVE', $_SERVER)) {
            return $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
        }

        return $_SERVER['HOME'] ?? null;
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
    public static function isFunc($funcName): bool
    {
        return is_callable($funcName) || (is_string($funcName) && function_exists($funcName) && is_callable($funcName));
    }

    /**
     * Set PHP execution time limit (doesn't work in safe mode)
     *
     * @param int $newLimit
     */
    public static function setTime($newLimit = 0): void
    {
        $newLimit = (int)$newLimit;

        self::iniSet('set_time_limit', $newLimit);
        self::iniSet('max_execution_time', $newLimit);
        if (self::isFunc('set_time_limit')) {
            set_time_limit($newLimit);
        }
    }

    /**
     * Set new memory limit
     *
     * @param string $newLimit
     */
    public static function setMemory($newLimit = '256M'): void
    {
        self::iniSet('memory_limit', $newLimit);
    }

    /**
     * @param string $version
     * @param string $current
     * @return bool
     */
    public static function isPHP($version, $current = PHP_VERSION): bool
    {
        $version = trim($version, '.');
        return preg_match('#^' . preg_quote($version, null) . '#i', $current);
    }

    /**
     * @param string $current
     * @return bool
     */
    public static function isPHP53($current = PHP_VERSION): bool
    {
        return self::isPHP('5.3', $current);
    }

    /**
     * @param string $current
     * @return bool
     */
    public static function isPHP7($current = PHP_VERSION): bool
    {
        return self::isPHP('7', $current);
    }

    /**
     * Get usage memory
     *
     * @param bool $isPeak
     * @return string
     */
    public static function getMemory($isPeak = true): string
    {
        if ($isPeak) {
            $memory = memory_get_peak_usage(false);
        } else {
            $memory = memory_get_usage(false);
        }

        return FS::format($memory);
    }

    /**
     * Return document root
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return string
     */
    public static function getDocRoot(): string
    {
        $result = '.';
        $root = Arr::key('DOCUMENT_ROOT', $_SERVER, true);

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
    public static function canCollectCodeCoverage(): bool
    {
        return self::hasXdebug() || self::hasPHPDBGCodeCoverage();
    }

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
    public static function getBinary(): string
    {
        // Custom PHP path
        if ((self::$binary === null) && (self::$binary = getenv('PHP_BINARY_CUSTOM')) === false) {
            self::$binary = PHP_BINARY;
        }

        // HHVM
        if (self::$binary === null && self::isHHVM()) {
            if ((self::$binary = getenv('PHP_BINARY')) === false) {
                self::$binary = PHP_BINARY;
            }
            self::$binary = escapeshellarg(self::$binary) . ' --php';
        }

        // PHP >= 5.4.0
        if (self::$binary === null && defined('PHP_BINARY')) {
            self::$binary = escapeshellarg(PHP_BINARY);
        }

        // PHP < 5.4.0
        if ((self::$binary === null) && PHP_SAPI === 'cli' && isset($_SERVER['_'])) {
            if (strpos($_SERVER['_'], 'phpunit') !== false) {
                $file = file($_SERVER['_']);

                if (strpos($file[0], ' ') !== false) {
                    $tmp = explode(' ', $file[0]);
                    self::$binary = escapeshellarg(trim($tmp[1]));
                } else {
                    self::$binary = escapeshellarg(ltrim(trim($file[0]), '#!'));
                }
            } elseif (strpos(basename($_SERVER['_']), 'php') !== false) {
                self::$binary = escapeshellarg($_SERVER['_']);
            }
        }

        if (self::$binary === null) {
            $binaryLocations = [
                PHP_BINDIR . '/php',
                PHP_BINDIR . '/php-cli.exe',
                PHP_BINDIR . '/php.exe',
            ];

            foreach ($binaryLocations as $binary) {
                if (is_readable($binary)) {
                    self::$binary = escapeshellarg($binary);
                    break;
                }
            }
        }

        if (self::$binary === null) {
            self::$binary = 'php';
        }

        return self::$binary;
    }

    /**
     * @return string
     */
    public static function getNameWithVersion(): string
    {
        return self::getName() . ' ' . self::getVersion();
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        if (self::isHHVM()) {
            return 'HHVM';
        }

        if (self::isPHPDBG()) {
            return 'PHPDBG';
        }

        return 'PHP';
    }

    /**
     * @return string
     */
    public static function getVendorUrl(): string
    {
        if (self::isHHVM()) {
            return 'http://hhvm.com/';
        }

        return 'http://php.net/';
    }

    /**
     * @return string
     */
    public static function getVersion(): ?string
    {
        if (self::isHHVM()) {
            return defined('HHVM_VERSION') ? HHVM_VERSION : null;
        }

        return defined('PHP_VERSION') ? PHP_VERSION : null;
    }

    /**
     * Returns true when the runtime used is PHP and Xdebug is loaded.
     *
     * @return bool
     */
    public static function hasXdebug(): bool
    {
        return (self::isRealPHP() || self::isHHVM()) && extension_loaded('xdebug');
    }

    /**
     * Returns true when the runtime used is HHVM.
     *
     * @return bool
     */
    public static function isHHVM(): bool
    {
        return defined('HHVM_VERSION');
    }

    /**
     * Returns true when the runtime used is PHP without the PHPDBG SAPI.
     *
     * @return bool
     */
    public static function isRealPHP(): bool
    {
        return !self::isHHVM() && !self::isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI.
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public static function isPHPDBG(): bool
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
    public static function hasPHPDBGCodeCoverage(): bool
    {
        return self::isPHPDBG() && function_exists('phpdbg_start_oplog');
    }
}
