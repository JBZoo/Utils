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

final class Env
{
    public const VAR_NULL   = 1;
    public const VAR_BOOL   = 2;
    public const VAR_INT    = 4;
    public const VAR_FLOAT  = 8;
    public const VAR_STRING = 16;

    /**
     * Returns an environment variable.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function get(
        string $envVarName,
        null|bool|float|int|string $default = null,
        int $options = self::VAR_STRING,
    ): null|bool|float|int|string {
        $envKey = \trim($envVarName);

        $value = \getenv($envKey);
        if ($value === false) {
            if (\array_key_exists($envKey, $_ENV)) {
                $envValue = (string)($_ENV[$envKey] ?? '');

                return self::convert($envValue, $options);
            }

            return $default;
        }

        return self::convert($value, $options);
    }

    /**
     * Converts the type of values like "true", "false", "null" or "123".
     */
    public static function convert(?string $value, int $options = self::VAR_STRING): null|bool|float|int|string
    {
        $cleanedValue = \trim(Filter::stripQuotes((string)$value));

        if (($options & self::VAR_NULL) > 0) {
            $cleanedValue = \strtolower($cleanedValue);
            if (\in_array($cleanedValue, ['null', 'nil', 'undefined'], true)) {
                return null;
            }
        }

        if (($options & self::VAR_STRING) > 0) {
            return $cleanedValue;
        }

        if (($options & self::VAR_FLOAT) > 0) {
            return Filter::float($cleanedValue);
        }

        if (($options & self::VAR_INT) > 0) {
            return Filter::int((int)$cleanedValue);
        }

        if (($options & self::VAR_BOOL) > 0) {
            return Filter::bool($cleanedValue);
        }

        return $value;
    }

    /**
     * Convert value of environment variable to clean string.
     */
    public static function string(string $envVarName, string $default = ''): string
    {
        if (self::isExists($envVarName)) {
            return (string)self::get($envVarName, $default, self::VAR_STRING);
        }

        return $default;
    }

    /**
     * Convert value of environment variable to strict integer value.
     */
    public static function int(string $envVarName, int $default = 0): int
    {
        if (self::isExists($envVarName)) {
            return (int)self::get($envVarName, $default, self::VAR_INT);
        }

        return $default;
    }

    /**
     * Convert value of environment variable to strict float value.
     */
    public static function float(string $envVarName, float $default = 0.0): float
    {
        if (self::isExists($envVarName)) {
            return (float)self::get($envVarName, $default, self::VAR_FLOAT);
        }

        return $default;
    }

    /**
     * Convert value of environment variable to strict bool value.
     */
    public static function bool(string $envVarName, bool $default = false): bool
    {
        if (self::isExists($envVarName)) {
            return (bool)self::get($envVarName, $default, self::VAR_BOOL);
        }

        return $default;
    }

    /**
     * Returns true if environment variable exists.
     */
    public static function isExists(string $envVarName): bool
    {
        return self::get($envVarName, null, self::VAR_NULL) !== null;
    }
}
