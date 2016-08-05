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
 * Class Str
 * @package JBZoo\Utils
 */
class Str
{
    /**
     * Default charset is UTF-8
     * @var string
     */
    public static $encoding = 'UTF-8';

    /**
     * Strip all witespaces from the given string.
     *
     * @param  string $string The string to strip
     * @return string
     */
    public static function stripSpace($string)
    {
        return preg_replace('/\s+/', '', $string);
    }

    /**
     * Parse text by lines
     *
     * @param string $text
     * @param bool   $toAssoc
     * @return array
     */
    public static function parseLines($text, $toAssoc = true)
    {
        $text = htmlspecialchars_decode($text);
        $text = self::clean($text, false, false);

        $text  = str_replace(array("\n", "\r", "\r\n", PHP_EOL), "\n", $text);
        $lines = explode("\n", $text);

        $result = array();
        if (!empty($lines)) {
            foreach ($lines as $line) {
                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                if ($toAssoc) {
                    $result[$line] = $line;
                } else {
                    $result[] = $line;
                }
            }
        }

        return $result;
    }

    /**
     * Make string safe
     * - Remove UTF-8 chars
     * - Remove all tags
     * - Trim
     * - Addslashes (opt)
     * - To lower (opt)
     *
     * @param string $string
     * @param bool   $toLower
     * @param bool   $addslashes
     * @return string
     */
    public static function clean($string, $toLower = false, $addslashes = false)
    {
        $string = Slug::removeAccents($string);
        $string = strip_tags($string);
        $string = trim($string);

        if ($addslashes) {
            $string = addslashes($string);
        }

        if ($toLower) {
            $string = self::low($string);
        }

        return $string;
    }

    /**
     * Convert >, <, ', " and & to html entities, but preserves entities that are already encoded.
     *
     * @param string $string The text to be converted
     * @param bool   $encodedEntities
     * @return string
     */
    public static function htmlEnt($string, $encodedEntities = false)
    {
        if ($encodedEntities) {
            // @codeCoverageIgnoreStart
            if (defined('HHVM_VERSION')) {
                $transTable = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
            } else {
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $transTable = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES, self::$encoding);
            }
            // @codeCoverageIgnoreEnd

            $transTable[chr(38)] = '&';

            $regExp = '/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/';

            return preg_replace($regExp, '&amp;', strtr($string, $transTable));
        }

        return htmlentities($string, ENT_QUOTES, self::$encoding);
    }

    /**
     * Get unique string
     *
     * @param  string $prefix
     * @return string
     */
    public static function unique($prefix = 'unique')
    {
        $prefix = rtrim(trim($prefix), '-');
        $random = mt_rand(10000000, 99999999);

        $result = $random;
        if ($prefix) {
            $result = $prefix . '-' . $random;
        }

        return $result;
    }

    /**
     * Generate ridable random string
     *
     * @param int  $length
     * @param bool $isReadable
     * @return string
     */
    public static function random($length = 10, $isReadable = true)
    {
        $result = '';

        if ($isReadable) {
            $vocal = array('a', 'e', 'i', 'o', 'u', '0');
            $conso = array('b', 'c', 'd', 'f', 'g',
                'h', 'j', 'k', 'l', 'm', 'n', 'p',
                'r', 's', 't', 'v', 'w', 'x', 'y', 'z',
                '1', '2', '3', '4', '5', '6', '7', '8', '9',
            );

            $max = $length / 2;

            for ($pos = 1; $pos <= $max; $pos++) {
                $result .= $conso[mt_rand(0, count($conso) - 1)];
                $result .= $vocal[mt_rand(0, count($vocal) - 1)];
            }

        } else {
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

            for ($pos = 0; $pos < $length; $pos++) {
                $result .= $chars[mt_rand() % strlen($chars)];
            }
        }

        return $result;
    }

    /**
     * Pads a given string with zeroes on the left.
     *
     * @param  int $number The number to pad
     * @param  int $length The total length of the desired string
     * @return string
     */
    public static function zeroPad($number, $length)
    {
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Truncate a string to a specified length without cutting a word off.
     *
     * @param   string  $string The string to truncate
     * @param   integer $length The length to truncate the string to
     * @param   string  $append Text to append to the string IF it gets truncated, defaults to '...'
     * @return  string
     */
    public static function truncateSafe($string, $length, $append = '...')
    {
        $result    = self::sub($string, 0, $length);
        $lastSpace = self::rpos($result, ' ');

        if ($lastSpace !== false && $string != $result) {
            $result = self::sub($result, 0, $lastSpace);
        }

        if ($result != $string) {
            $result .= $append;
        }

        return $result;
    }

    /**
     * Truncate the string to given length of characters.
     *
     * @param string  $string The variable to truncate
     * @param integer $limit  The length to truncate the string to
     * @param string  $append Text to append to the string IF it gets truncated, defaults to '...'
     * @return string
     */
    public static function limitChars($string, $limit = 100, $append = '...')
    {
        if (self::len($string) <= $limit) {
            return $string;
        }

        return rtrim(self::sub($string, 0, $limit)) . $append;
    }

    /**
     * Truncate the string to given length of words.
     *
     * @param string $string
     * @param int    $limit
     * @param string $append
     * @return string
     */
    public static function limitWords($string, $limit = 100, $append = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);

        if (!Arr::key(0, $matches) || self::len($string) === self::len($matches[0])) {
            return $string;
        }

        return rtrim($matches[0]) . $append;
    }

    /**
     * Check if a given string matches a given pattern.
     *
     * @param  string $pattern Parttern of string exptected
     * @param  string $string  String that need to be matched
     * @param  bool   $caseSensitive
     * @return bool
     */
    public static function like($pattern, $string, $caseSensitive = true)
    {
        if ($pattern == $string) {
            return true;
        }

        // Preg flags
        $flags = $caseSensitive ? '' : 'i';

        // Escape any regex special characters
        $pattern = preg_quote($pattern, '#');

        // Unescape * which is our wildcard character and change it to .*
        $pattern = str_replace('\*', '.*', $pattern);

        return (bool)preg_match('#^' . $pattern . '$#' . $flags, $string);
    }

    /**
     * Converts any accent characters to their equivalent normal characters
     *
     * @param string $text
     * @param bool   $isCache
     * @return string
     */
    public static function slug($text = '', $isCache = false)
    {
        static $cache = array();

        if (!$isCache) {
            return Slug::filter($text);

        } elseif (!array_key_exists($text, $cache)) { // Not Arr::key() for performance
            $cache[$text] = Slug::filter($text);
        }

        return $cache[$text];
    }

    /**
     * Check is mbstring oeverload standard functions
     *
     * @return int
     */
    public static function isOverload()
    {
        if (self::isMBString()) {
            return (int)Sys::iniGet('mbstring.func_overload') & MB_OVERLOAD_STRING;
        }

        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Check is mbstring loaded
     *
     * @return bool
     */
    public static function isMBString()
    {
        static $isLoaded;

        if (null === $isLoaded) {
            $isLoaded = extension_loaded('mbstring');

            if ($isLoaded) {
                mb_internal_encoding(self::$encoding);
            }
        }

        return $isLoaded;
    }

    /**
     * Get string length
     *
     * @param $string
     * @return int
     */
    public static function len($string)
    {
        if (self::isMBString()) {
            return mb_strlen($string, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return strlen($string);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Find position of first occurrence of string in a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @return int
     */
    public static function pos($haystack, $needle, $offset = 0)
    {
        if (self::isMBString()) {
            return mb_strpos($haystack, $needle, $offset, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return strpos($haystack, $needle, $offset);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Find position of last occurrence of a string in a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @return int
     */
    public static function rpos($haystack, $needle, $offset = 0)
    {
        if (self::isMBString()) {
            return mb_strrpos($haystack, $needle, $offset, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return strrpos($haystack, $needle, $offset);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Finds position of first occurrence of a string within another, case insensitive
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @return int
     */
    public static function ipos($haystack, $needle, $offset = 0)
    {
        if (self::isMBString()) {
            return mb_stripos($haystack, $needle, $offset, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return stripos($haystack, $needle, $offset);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Finds first occurrence of a string within another
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $beforeNeedle
     * @return string
     */
    public static function strstr($haystack, $needle, $beforeNeedle = false)
    {
        if (self::isMBString()) {
            return mb_strstr($haystack, $needle, $beforeNeedle, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return strstr($haystack, $needle, $beforeNeedle);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Finds first occurrence of a string within another, case insensitive
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $beforeNeedle
     * @return string
     */
    public static function istr($haystack, $needle, $beforeNeedle = false)
    {
        if (self::isMBString()) {
            return mb_stristr($haystack, $needle, $beforeNeedle, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return stristr($haystack, $needle, $beforeNeedle);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Finds the last occurrence of a character in a string within another
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $part
     * @return string
     */
    public static function rchr($haystack, $needle, $part = null)
    {
        if (self::isMBString()) {
            return mb_strrchr($haystack, $needle, $part, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return strrchr($haystack, $needle);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Get part of string
     *
     * @param string $string
     * @param int    $start
     * @param int    $length
     * @return string
     */
    public static function sub($string, $start, $length = 0)
    {
        if (self::isMBString()) {
            if (0 == $length) {
                $length = self::len($string);
            }

            return mb_substr($string, $start, $length, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return substr($string, $start, $length);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Make a string lowercase
     *
     * @param string $string
     * @return string
     */
    public static function low($string)
    {
        if (self::isMBString()) {
            return mb_strtolower($string, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return strtolower($string);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Make a string uppercase
     *
     * @param string $string
     * @return string
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function up($string)
    {
        if (self::isMBString()) {
            return mb_strtoupper($string, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return strtoupper($string);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Count the number of substring occurrences
     *
     * @param string $haystack
     * @param string $needle
     * @return int
     */
    public static function subCount($haystack, $needle)
    {
        if (self::isMBString()) {
            return mb_substr_count($haystack, $needle, self::$encoding);

        } else {
            // @codeCoverageIgnoreStart
            return substr_count($haystack, $needle);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Checks if the $haystack starts with the text in the $needle.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $caseSensitive
     * @return bool
     */
    public static function isStart($haystack, $needle, $caseSensitive = false)
    {
        if ($caseSensitive) {
            return $needle === '' || self::pos($haystack, $needle) === 0;
        } else {
            return $needle === '' || self::ipos($haystack, $needle) === 0;
        }
    }

    /**
     * Checks if the $haystack ends with the text in the $needle. Case sensitive.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $caseSensitive
     * @return bool
     */
    public static function isEnd($haystack, $needle, $caseSensitive = false)
    {
        if ($caseSensitive) {
            return $needle === '' || self::sub($haystack, -self::len($needle)) === $needle;

        } else {
            $haystack = self::low($haystack);
            $needle   = self::low($needle);

            return $needle === '' || self::sub($haystack, -self::len($needle)) === $needle;
        }
    }

    /**
     * Trim whitespaces and other special chars
     *
     * @param string $value
     * @param bool   $extendMode
     * @return string
     */
    public static function trim($value, $extendMode = false)
    {
        $result = (string)trim($value);

        if ($extendMode) {
            $result = trim($result, chr(0xE3) . chr(0x80) . chr(0x80));
            $result = trim($result, chr(0xC2) . chr(0xA0));
            $result = trim($result);
        }

        return $result;
    }

    /**
     * Escape string before save it as xml content
     *
     * @param $string
     * @return mixed
     */
    public static function escXml($string)
    {
        $string = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);

        $string = str_replace(
            array("&", "<", ">", '"', "'"),
            array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"),
            $string
        );

        return $string;
    }

    /**
     * Escape UTF-8 strings
     *
     * @param string $string
     * @return string
     */
    public static function esc($string)
    {
        return htmlspecialchars($string, ENT_NOQUOTES, self::$encoding);
    }

    /**
     * Convert camel case to human readable format
     *
     * @param string $input
     * @param string $separator
     * @param bool   $toLower*
     * @return string
     */
    public static function splitCamelCase($input, $separator = '_', $toLower = true)
    {
        $original = $input;

        $output = preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), '_$0', $input);
        $output = preg_replace('#_{1,}#', $separator, $output);

        $output = trim($output);
        if ($toLower) {
            $output = strtolower($output);
        }

        if (strlen($output) == 0) {
            return $original;
        }

        return $output;
    }

    /**
     * Convert test name to human readable string
     *
     * @param string $input
     * @return mixed|string
     */
    public static function testName2Human($input)
    {
        $original = $input;
        $input    = self::getClassName($input, false);

        if (!preg_match('#^tests#i', $input)) {
            $input = preg_replace('#^(test)#i', '', $input);
        }

        $input  = preg_replace('#(test)$#i', '', $input);
        $output = preg_replace(array('/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'), ' $0', $input);
        $output = trim(str_replace('_', ' ', $output));

        $output = implode(' ', array_filter(array_map(function ($item) {
            $item = ucwords($item);
            $item = trim($item);

            return $item;
        }, explode(' ', $output))));


        if (strcasecmp($original, $output) === 0) {
            return $original;
        }

        if (strlen($output) == 0) {
            return $original;
        }

        return $output;
    }

    /**
     * Generates a universally unique identifier (UUID v4) according to RFC 4122
     * Version 4 UUIDs are pseudo-random!
     *
     * Returns Version 4 UUID format: xxxxxxxx-xxxx-4xxx-Yxxx-xxxxxxxxxxxx where x is
     * any random hex digit and Y is a random choice from 8, 9, a, or b.
     *
     * @see http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
     *
     * @return string
     */
    public static function uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Get class name without namespace
     *
     * @param mixed $object
     * @param bool  $toLower
     * @return mixed|string
     */
    public static function getClassName($object, $toLower = false)
    {
        if (is_object($object)) {
            $className = get_class($object);
        } else {
            $className = $object;
        }

        $result = $className;
        if (strpos($className, '\\') !== false) {
            $className = explode('\\', $className);
            reset($className);
            $result = end($className);
        }

        if ($toLower) {
            $result = strtolower($result);
        }

        return $result;
    }

    /**
     * Increments a trailing number in a string.
     * Used to easily create distinct labels when copying objects. The method has the following styles:
     *  - default: "Label" becomes "Label (2)"
     *  - dash:    "Label" becomes "Label-2"
     *
     * @param   string  $string The source string.
     * @param   string  $style  The the style (default|dash).
     * @param   integer $next   If supplied, this number is used for the copy, otherwise it is the 'next' number.
     * @return  string
     */
    public static function inc($string, $style = 'default', $next = 0)
    {
        $styles = array(
            'dash'    => array(
                '#-(\d+)$#', '-%d'
            ),
            'default' => array(
                array('#\((\d+)\)$#', '#\(\d+\)$#'),
                array(' (%d)', '(%d)'),
            ),
        );

        $styleSpec = isset($styles[$style]) ? $styles[$style] : $styles['default'];

        // Regular expression search and replace patterns.
        if (is_array($styleSpec[0])) {
            $rxSearch  = $styleSpec[0][0];
            $rxReplace = $styleSpec[0][1];
        } else {
            $rxSearch = $rxReplace = $styleSpec[0];
        }

        // New and old (existing) sprintf formats.
        if (is_array($styleSpec[1])) {
            $newFormat = $styleSpec[1][0];
            $oldFormat = $styleSpec[1][1];
        } else {
            $newFormat = $oldFormat = $styleSpec[1];
        }

        // Check if we are incrementing an existing pattern, or appending a new one.
        if (preg_match($rxSearch, $string, $matches)) {
            $next   = empty($next) ? ($matches[1] + 1) : $next;
            $string = preg_replace($rxReplace, sprintf($oldFormat, $next), $string);
        } else {
            $next = empty($next) ? 2 : $next;
            $string .= sprintf($newFormat, $next);
        }

        return $string;
    }


    /**
     * Splits a string of multiple queries into an array of individual queries.
     * Single line or line end comments and multi line comments are stripped off.
     *
     * @param   string $sql Input SQL string with which to split into individual queries.
     * @return  array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function splitSql($sql)
    {
        $start     = 0;
        $open      = false;
        $comment   = false;
        $endString = '';
        $end       = strlen($sql);
        $queries   = array();
        $query     = '';

        for ($i = 0; $i < $end; $i++) {
            $current      = substr($sql, $i, 1);
            $current2     = substr($sql, $i, 2);
            $current3     = substr($sql, $i, 3);
            $lenEndString = strlen($endString);
            $testEnd      = substr($sql, $i, $lenEndString);

            if ($current == '"' || $current == "'" || $current2 == '--'
                || ($current2 == '/*' && $current3 != '/*!' && $current3 != '/*+')
                || ($current == '#' && $current3 != '#__')
                || ($comment && $testEnd == $endString)
            ) {
                // Check if quoted with previous backslash
                $num = 2;

                while (substr($sql, $i - $num + 1, 1) == '\\' && $num < $i) {
                    $num++;
                }

                // Not quoted
                if ($num % 2 == 0) {
                    if ($open) {
                        if ($testEnd == $endString) {
                            if ($comment) {
                                $comment = false;
                                if ($lenEndString > 1) {
                                    $i += ($lenEndString - 1);
                                    $current = substr($sql, $i, 1);
                                }
                                $start = $i + 1;
                            }
                            $open      = false;
                            $endString = '';
                        }
                    } else {
                        $open = true;
                        if ($current2 == '--') {
                            $endString = "\n";
                            $comment   = true;
                        } elseif ($current2 == '/*') {
                            $endString = '*/';
                            $comment   = true;
                        } elseif ($current == '#') {
                            $endString = "\n";
                            $comment   = true;
                        } else {
                            $endString = $current;
                        }
                        if ($comment && $start < $i) {
                            $query = $query . substr($sql, $start, ($i - $start));
                        }
                    }
                }
            }

            if ($comment) {
                $start = $i + 1;
            }

            if (($current == ';' && !$open) || $i == $end - 1) {
                if ($start <= $i) {
                    $query = $query . substr($sql, $start, ($i - $start + 1));
                }
                $query = trim($query);

                if ($query) {
                    if (($i == $end - 1) && ($current != ';')) {
                        $query = $query . ';';
                    }
                    $queries[] = $query;
                }

                $query = '';
                $start = $i + 1;
            }
        }

        return $queries;
    }
}
