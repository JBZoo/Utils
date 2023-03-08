<?php

/**
 * JBZoo Toolbox - Utils.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Utils
 */

declare(strict_types=1);

namespace JBZoo\Utils;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class Sys
{
    /**
     * Check is current OS Windows.
     */
    public static function isWin(): bool
    {
        return \strncasecmp(\PHP_OS_FAMILY, 'WIN', 3) === 0 || \DIRECTORY_SEPARATOR === '\\';
    }

    /**
     * Check is current user ROOT.
     */
    public static function isRoot(): bool
    {
        if (self::isFunc('posix_geteuid')) {
            return \posix_geteuid() === 0;
        }

        return false;
    }

    /**
     * Returns current linux user who runs script.
     */
    public static function getUserName(): ?string
    {
        /** @phpstan-ignore-next-line */
        $userInfo = (array)\posix_getpwuid(\posix_geteuid());

        /** @phpstan-ignore-next-line */
        return $userInfo['name'] ?? null;
    }

    /**
     * Returns a home directory of current user.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getHome(): ?string
    {
        /** @phpstan-ignore-next-line */
        $userInfo = (array)\posix_getpwuid(\posix_geteuid());
        if (isset($userInfo['dir'])) {
            return $userInfo['dir'];
        }

        if (\array_key_exists('HOMEDRIVE', $_SERVER)) {
            return $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
        }

        return $_SERVER['HOME'] ?? null;
    }

    /**
     * Alias fo ini_set function.
     */
    public static function iniSet(string $phpIniKey, string $newValue): bool
    {
        if (self::isFunc('ini_set')) {
            return Filter::bool(\ini_set($phpIniKey, $newValue));
        }

        return false;
    }

    /**
     * Alias fo ini_get function.
     */
    public static function iniGet(string $varName): string
    {
        return (string)\ini_get($varName);
    }

    /**
     * Checks if function exists and callable.
     */
    public static function isFunc(\Closure|string $funcName): bool
    {
        $disabledOnPhpIni = false;

        if (\is_string($funcName)) {
            $disabledOnPhpIni = \str_contains(
                \strtolower(self::iniGet('disable_functions')),
                \strtolower(\trim($funcName)),
            );
        }

        return !$disabledOnPhpIni && (\is_callable($funcName) || \function_exists($funcName));
    }

    /**
     * Set PHP execution time limit (doesn't work in safe mode).
     */
    public static function setTime(int $newLimit = 0): void
    {
        self::iniSet('set_time_limit', (string)$newLimit);
        self::iniSet('max_execution_time', (string)$newLimit);

        if (self::isFunc('set_time_limit')) {
            \set_time_limit($newLimit);
        }
    }

    /**
     * Set new memory limit.
     */
    public static function setMemory(string $newLimit = '256M'): void
    {
        self::iniSet('memory_limit', $newLimit);
    }

    /**
     * Compares PHP versions.
     */
    public static function isPHP(string $version, string $current = \PHP_VERSION): bool
    {
        $version = \trim($version, '.');

        return (bool)\preg_match('#^' . \preg_quote($version, '') . '#i', $current);
    }

    /**
     * Get usage memory.
     */
    public static function getMemory(bool $isPeak = true): string
    {
        if ($isPeak) {
            $memory = \memory_get_peak_usage(false);
        } else {
            $memory = \memory_get_usage(false);
        }

        return FS::format($memory);
    }

    /**
     * Returns current document root.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getDocRoot(): ?string
    {
        $result = $_SERVER['DOCUMENT_ROOT'] ?? '.';
        $result = FS::clean($result);
        $result = FS::real($result);

        if (isStrEmpty($result)) {
            $result = FS::real('.');
        }

        return $result;
    }

    /**
     * Returns true when Xdebug is supported or
     * the runtime used is PHPDBG (PHP >= 7.0).
     */
    public static function canCollectCodeCoverage(): bool
    {
        return self::hasXdebug() || self::hasPHPDBGCodeCoverage();
    }

    /**
     * Returns the path to the binary of the current runtime.
     * Appends ' --php' to the path when the runtime is HHVM.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getBinary(): string
    {
        $customPath = Env::string('PHP_BINARY_CUSTOM');
        if (!isStrEmpty($customPath)) {
            return $customPath;
        }

        // HHVM
        if (self::isHHVM()) {
            if (($binary = \getenv('PHP_BINARY')) === false) {
                $binary = \PHP_BINARY;
            }

            return \escapeshellarg($binary) . ' --php';
        }

        if (\defined('PHP_BINARY')) {
            return \escapeshellarg(\PHP_BINARY);
        }

        $binaryLocations = [
            \PHP_BINDIR . '/php',
            \PHP_BINDIR . '/php-cli.exe',
            \PHP_BINDIR . '/php.exe',
        ];

        foreach ($binaryLocations as $binary) {
            if (\is_readable($binary)) {
                return $binary;
            }
        }

        return 'php';
    }

    /**
     * Return type and version of current PHP.
     */
    public static function getNameWithVersion(): string
    {
        $name    = self::getName();
        $version = self::getVersion();

        return \trim("{$name} {$version}");
    }

    /**
     * Returns type of PHP.
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
     * Return URL of PHP official web-site. It depends on PHP vendor.
     */
    public static function getVendorUrl(): string
    {
        if (self::isHHVM()) {
            return 'http://hhvm.com/';
        }

        return 'http://php.net/';
    }

    /**
     * Returns current PHP version.
     */
    public static function getVersion(): ?string
    {
        return \defined('PHP_VERSION') ? \PHP_VERSION : null;
    }

    /**
     * Returns true when the runtime used is PHP and Xdebug is loaded.
     */
    public static function hasXdebug(): bool
    {
        return (self::isRealPHP() || self::isHHVM()) && \extension_loaded('xdebug');
    }

    /**
     * Returns true when the runtime used is HHVM.
     */
    public static function isHHVM(): bool
    {
        return \defined('HHVM_VERSION');
    }

    /**
     * Returns true when the runtime used is PHP without the PHPDBG SAPI.
     */
    public static function isRealPHP(): bool
    {
        return !self::isHHVM() && !self::isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI.
     */
    public static function isPHPDBG(): bool
    {
        return \PHP_SAPI === 'phpdbg' && !self::isHHVM();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI
     * and the phpdbg_*_oplog() functions are available (PHP >= 7.0).
     */
    public static function hasPHPDBGCodeCoverage(): bool
    {
        return self::isPHPDBG() && \function_exists('phpdbg_start_oplog');
    }
}
