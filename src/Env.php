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
     * @return string
     * @deprecated
     */
    public static function getBinary()
    {
        return Sys::getBinary();
    }

    /**
     * @return string
     * @deprecated
     */
    public static function getNameWithVersion()
    {
        return Sys::getNameWithVersion();
    }

    /**
     * @return string
     * @deprecated
     */
    public static function getName()
    {
        return Sys::getName();
    }

    /**
     * @return string
     * @deprecated
     */
    public static function getVendorUrl()
    {
        return Sys::getVendorUrl();
    }

    /**
     * @return string
     * @deprecated
     */
    public static function getVersion()
    {
        return Sys::getVersion();
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function hasXdebug()
    {
        return Sys::hasXdebug();
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function isHHVM()
    {
        return Sys::isHHVM();
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function isPHP()
    {
        return Sys::isRealPHP();
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function isPHPDBG()
    {
        return Sys::isPHPDBG();
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function hasPHPDBGCodeCoverage()
    {
        return Sys::hasPHPDBGCodeCoverage();
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
