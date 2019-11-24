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
     * @param string $name
     * @param string $default
     * @param int    $options
     * @return mixed
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function get($name, $default = null, $options = self::VAR_STRING)
    {
        $envKey = trim($name);

        $value = getenv($envKey);
        if ($value === false) {
            if (isset($_ENV[$envKey])) {
                return self::convert($_ENV[$envKey], $options);
            }
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
