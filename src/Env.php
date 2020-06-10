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
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\Utils;

/**
 * Class Env
 *
 * @package JBZoo\Utils
 */
class Env
{
    public const VAR_NULL   = 1;
    public const VAR_BOOL   = 2;
    public const VAR_INT    = 4;
    public const VAR_FLOAT  = 8;
    public const VAR_STRING = 16;

    /**
     * Returns an environment variable.
     *
     * @param string $envVarName
     * @param mixed  $default
     * @param int    $options
     * @return mixed
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function get(string $envVarName, $default = null, int $options = self::VAR_STRING)
    {
        $envKey = trim($envVarName);

        $value = getenv($envKey);
        if ($value === false) {
            if (array_key_exists($envKey, $_ENV)) {
                return self::convert($_ENV[$envKey], $options);
            }

            return $default;
        }

        return self::convert($value, $options);
    }

    /**
     * Converts the type of values like "true", "false", "null" or "123".
     *
     * @param string|null $value
     * @param int         $options
     * @return string|float|int|bool|null
     */
    public static function convert(?string $value, int $options = self::VAR_STRING)
    {
        if ($options & self::VAR_STRING && !empty($value)) {
            return trim(Filter::stripQuotes($value));
        }

        if ($options & self::VAR_FLOAT) {
            return Filter::float($value);
        }

        if ($options & self::VAR_INT) {
            return Filter::int((int)$value);
        }

        if ($options & self::VAR_BOOL || $options & self::VAR_NULL) {
            if (null === $value || 'null' === strtolower(trim($value))) {
                return null;
            }

            return Filter::bool($value);
        }

        return $value;
    }

    /**
     * @param string $envVarName
     * @param string $default
     * @return string
     */
    public static function string(string $envVarName, string $default = ''): string
    {
        return (string)self::get($envVarName, $default, self::VAR_STRING);
    }

    /**
     * @param string $envVarName
     * @param int    $default
     * @return int
     */
    public static function int(string $envVarName, int $default = 0): int
    {
        return (int)self::get($envVarName, $default, self::VAR_INT);
    }

    /**
     * @param string $envVarName
     * @param float  $default
     * @return float
     */
    public static function float(string $envVarName, float $default = 0.0): float
    {
        return (float)self::get($envVarName, $default, self::VAR_FLOAT);
    }

    /**
     * @param string $envVarName
     * @param bool   $default
     * @return bool
     */
    public static function bool(string $envVarName, bool $default = false): bool
    {
        return (bool)self::get($envVarName, $default, self::VAR_BOOL);
    }
}
