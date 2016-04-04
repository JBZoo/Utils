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
 * Class Filter
 * @package JBZoo\Utils
 */
class Vars
{
    /**
     * Converts many english words that equate to true or false to boolean.
     *
     * @param  string $string The string to convert to boolean
     * @return boolean
     *
     * @deprecated See JBZoo\Utils\Filter
     */
    public static function bool($string)
    {
        return Filter::bool($string);
    }

    /**
     * @param string $value
     * @param int    $round
     * @return float
     *
     * @deprecated See JBZoo\Utils\Filter
     */
    public static function float($value, $round = 10)
    {
        return Filter::float($value, $round);
    }

    /**
     * Smart convert any string to int
     *
     * @param string $value
     * @return int
     *
     * @deprecated See JBZoo\Utils\Filter
     */
    public static function int($value)
    {
        return Filter::int($value);
    }

    /**
     * Return only digits chars
     *
     * @param $value
     * @return mixed
     *
     * @deprecated See JBZoo\Utils\Filter
     */
    public static function digits($value)
    {
        return Filter::digits($value);
    }

    /**
     * Return only alpha chars
     *
     * @param $value
     * @return mixed
     *
     * @deprecated See JBZoo\Utils\Filter
     */
    public static function alpha($value)
    {
        return Filter::alpha($value);
    }

    /**
     * Return only alpha and digits chars
     *
     * @param $value
     * @return mixed
     *
     * @deprecated See JBZoo\Utils\Filter
     */
    public static function alphaDigets($value)
    {
        return Filter::alphanum($value);
    }

    /**
     * Validate emil
     *
     * @param $email
     * @return mixed
     *
     * @deprecated See JBZoo\Utils\Email
     */
    public static function email($email)
    {
        return Filter::email($email);
    }

    /**
     * Access an array index, retrieving the value stored there if it exists or a default if it does not.
     * This function allows you to concisely access an index which may or may not exist without raising a warning.
     *
     * @param  array $var     Array value to access
     * @param  mixed $default Default value to return if the key is not
     * @return mixed
     */
    public static function get(&$var, $default = null)
    {
        if (isset($var)) {
            return $var;
        }

        return $default;
    }

    /**
     * Return true if the number is within the min and max.
     *
     * @param int|float $number
     * @param int|float $min
     * @param int|float $max
     * @return bool
     */
    public static function isIn($number, $min, $max)
    {
        return ($number >= $min && $number <= $max);
    }

    /**
     * Is the current value even?
     *
     * @param int $number
     * @return bool
     */
    public static function isEven($number)
    {
        return ($number % 2 === 0);
    }

    /**
     * Is the current value negative; less than zero.
     *
     * @param int $number
     * @return bool
     */
    public static function isNegative($number)
    {
        return ($number < 0);
    }

    /**
     * Is the current value odd?
     *
     * @param int $number
     * @return bool
     */
    public static function isOdd($number)
    {
        return !self::isEven($number);
    }

    /**
     * Is the current value positive; greater than or equal to zero.
     *
     * @param int  $number
     * @param bool $zero
     * @return bool
     */
    public static function isPositive($number, $zero = true)
    {
        return ($zero ? ($number >= 0) : ($number > 0));
    }

    /**
     * Limits the number between two bounds.
     *
     * @param int $number
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function limit($number, $min, $max)
    {
        return self::max(self::min($number, $min), $max);
    }

    /**
     * Increase the number to the minimum if below threshold.
     *
     * @param int $number
     * @param int $min
     * @return int
     */
    public static function min($number, $min)
    {
        if ($number < $min) {
            $number = $min;
        }
        return $number;
    }

    /**
     * Decrease the number to the maximum if above threshold.
     *
     * @param int $number
     * @param int $max
     * @return int
     */
    public static function max($number, $max)
    {
        if ($number > $max) {
            $number = $max;
        }
        return $number;
    }

    /**
     * Return true if the number is outside the min and max.
     *
     * @param int $number
     * @param int $min
     * @param int $max
     * @return bool
     */
    public static function out($number, $min, $max)
    {
        return ($number < $min || $number > $max);
    }

    /**
     * Get relative percent
     *
     * @param float $normal
     * @param float $current
     * @return string
     */
    public static function relativePercent($normal, $current)
    {
        $normal  = (float)$normal;
        $current = (float)$current;

        if (!$normal || $normal == $current) {
            return '100';

        } else {
            $normal  = abs($normal);
            $percent = round($current / $normal * 100);

            return number_format($percent, 0, '.', ' ');
        }
    }
}
