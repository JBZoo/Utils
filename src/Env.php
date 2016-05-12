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
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 */

namespace JBZoo\Utils;

/**
 * Class Env
 * @package JBZoo\Utils
 */
class Env
{
    const VAR_NULL   = 1;
    const VAR_BOOL   = 2;
    const VAR_INT    = 4;
    const VAR_FLOAT  = 8;
    const VAR_STRING = 16;

    /**
     * @var string
     */
    private static $_binary;

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
        return (self::isPHP() || self::isHHVM()) && extension_loaded('xdebug');
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
    public static function isPHP()
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

    /**
     * Returns an environment variable.
     *
     * @param string $name
     * @param string $default
     * @param int    $options
     * @return mixed
     */
    public static function get($name, $default = null, $options = self::VAR_STRING)
    {
        $value = getenv(trim($name));

        if ($value === false) {
            return $default;
        }

        return self::convert($value, $options);
    }

    /**
     * Converts the type of values like "true", "false", "null" or "123".
     *
     * @param string $value
     * @param int    $options
     * @return mixed
     */
    public static function convert($value, $options = self::VAR_STRING)
    {
        $options = (int)$options;

        if ($options & self::VAR_STRING && !empty($value)) {
            return trim(Filter::stripQuotes($value));
        }

        if ($options & self::VAR_FLOAT) {
            return Filter::float($value, 12);
        }

        if ($options & self::VAR_INT) {
            return Filter::int($value);
        }

        if ($options & self::VAR_BOOL || $options & self::VAR_NULL) {
            if (null === $value || 'null' === strtolower(trim($value))) {
                return null;
            }

            return Filter::bool($value);
        }

        return (string)$value;
    }
}
