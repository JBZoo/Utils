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
 * Class Filter
 * @package JBZoo\Utils
 */
class Vars
{
    /**
     * Converts many english words that equate to true or false to boolean.
     *
     * @param  string $string  The string to convert to boolean
     * @param  bool   $default The value to return if we can't match any yes/no words
     * @return boolean
     */
    public static function bool($string, $default = false)
    {

        $yesList = array('affirmative', 'all right', 'aye', 'indubitably', 'most assuredly', 'ok', 'of course', 'oui',
            'okay', 'sure thing', 'y', 'yes', 'yea', 'yep', 'sure', 'yeah', 'true', 't', 'on', '1', 'vrai',
            'да', 'д');

        $noList = array('no*', 'no way', 'nope', 'nah', 'na', 'never', 'absolutely not', 'by no means', 'negative',
            'never ever', 'false', 'f', 'off', '0', 'non', 'faux', 'нет', 'н', 'немає');

        $string = Str::low($string);

        if (Arr::in($string, $yesList) || self::float($string) > 0) {
            return true;

        } elseif (Arr::in($string, $noList)) {
            return false;
        }

        return (bool)$default;
    }

    /**
     * @param string $value
     * @param int    $round
     * @return float
     */
    public static function float($value, $round = 10)
    {
        $value = preg_replace('#[^0-9\-\.\,]#ius', '', $value);
        $value = trim($value);
        $value = str_replace(array(' ', ','), array('', '.'), $value);
        $value = (float)$value;
        $value = round($value, $round);

        return $value;
    }

    /**
     * Smart convert any string to int
     *
     * @param string $value
     * @return int
     */
    public static function int($value)
    {
        return (int)preg_replace('#[^0-9\-]#ius', '', $value);
    }

    /**
     * Return only digits chars
     *
     * @param $value
     * @return mixed
     */
    public static function digits($value)
    {
        return preg_replace('#[^0-9]#ius', '', $value);
    }

    /**
     * Return only alpha chars
     *
     * @param $value
     * @return mixed
     */
    public static function alpha($value)
    {
        return preg_replace('#[^a-z]#ius', '', $value);
    }

    /**
     * Return only alpha and digits chars
     *
     * @param $value
     * @return mixed
     */
    public static function alphaDigets($value)
    {
        return preg_replace('#[^a-z0-9]#ius', '', $value);
    }

    /**
     * Validate emil
     *
     * @param $email
     * @return mixed
     */
    public static function email($email)
    {
        $email = trim($email);
        $regex = chr(1) . '^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$' . chr(1) . 'u';

        if (preg_match($regex, $email) && (bool)filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        return false;
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
}
