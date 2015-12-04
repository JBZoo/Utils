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
 * Class Serialize
 * @package JBZoo\Utils
 */
class Ser
{
    /**
     * Check value to find if it was serialized.
     * If $data is not an string, then returned value will always be false. Serialized data is always a string.
     *
     * @param  mixed $data Value to check to see if was serialized
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function is($data)
    {
        // If it isn't a string, it isn't serialized
        if (!is_string($data)) {
            return false;
        }

        $data = trim($data);

        // Is it the serialized NULL value?
        if ($data === 'N;') {
            return true;

        } elseif ($data === 'b:0;' || $data === 'b:1;') { // Is it a serialized boolean?
            return true;
        }

        $length = strlen($data);

        // Check some basic requirements of all serialized strings
        if (self::_checkBasic($data, $length)) {
            return false;
        }

        return @unserialize($data) !== false;
    }

    /**
     * Serialize data, if needed.
     *
     * @param  mixed $data Data that might need to be serialized
     * @return mixed
     */
    public static function maybe($data)
    {
        if (is_array($data) || is_object($data)) {
            return serialize($data);
        }

        return $data;
    }

    /**
     * Unserialize value only if it is serialized.
     *
     * @param  string $data A variable that may or may not be serialized
     * @return mixed
     */
    public static function maybeUn($data)
    {
        // If it isn't a string, it isn't serialized
        if (!is_string($data)) {
            return $data;
        }

        $data = trim($data);

        // Is it the serialized NULL value?
        if ($data === 'N;') {
            return null;
        }

        $length = strlen($data);

        // Check some basic requirements of all serialized strings
        if (self::_checkBasic($data, $length)) {
            return $data;
        }

        // $data is the serialized false value
        if ($data === 'b:0;') {
            return false;
        }

        // Don't attempt to unserialize data that isn't serialized
        $uns = @unserialize($data);

        // Data failed to unserialize?
        if ($uns === false) {
            $uns = @unserialize(self::fix($data));

            if ($uns === false) {
                return $data;

            } else {
                return $uns;
            }

        } else {
            return $uns;
        }
    }

    /**
     * Unserializes partially-corrupted arrays that occur sometimes. Addresses
     * specifically the `unserialize(): Error at offset xxx of yyy bytes` error.
     *
     * NOTE: This error can *frequently* occur with mismatched character sets and higher-than-ASCII characters.
     * Contributed by Theodore R. Smith of PHP Experts, Inc. <http://www.phpexperts.pro/>
     *
     * @param  string $brokenSerializedData
     * @return string
     */
    public static function fix($brokenSerializedData)
    {
        $fixdSerializedData = preg_replace_callback('!s:(\d+):"(.*?)";!', function ($matches) {
            $snip = $matches[2];
            return 's:' . strlen($snip) . ':"' . $snip . '";';
        }, $brokenSerializedData);

        return $fixdSerializedData;
    }

    /**
     * Check some basic requirements of all serialized strings
     *
     * @param string $data
     * @param int    $length
     * @return bool
     */
    protected static function _checkBasic($data, $length)
    {
        return $length < 4 || $data[1] !== ':' || ($data[$length - 1] !== ';' && $data[$length - 1] !== '}');
    }
}
