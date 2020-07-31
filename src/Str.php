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
 * Class Str
 *
 * @package JBZoo\Utils
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Str
{
    /**
     * Default charset is UTF-8
     *
     * @var string
     */
    public static $encoding = 'UTF-8';

    /**
     * Strip all whitespaces from the given string.
     *
     * @param string $string The string to strip
     * @return string
     */
    public static function stripSpace(string $string): string
    {
        return (string)preg_replace('/\s+/', '', $string);
    }

    /**
     * Parse text by lines
     *
     * @param string $text
     * @param bool   $toAssoc
     * @return array
     */
    public static function parseLines(string $text, bool $toAssoc = true): array
    {
        $text = htmlspecialchars_decode($text);
        $text = self::clean($text, false, false, false);

        $text = str_replace(["\n", "\r", "\r\n", PHP_EOL], "\n", $text);
        $lines = (array)explode("\n", $text);

        $result = [];
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

        return $result;
    }

    /**
     * Make string safe
     * - Remove UTF-8 chars
     * - Remove all tags
     * - Trim
     * - Add Slashes (opt)
     * - To lower (opt)
     *
     * @param string $string
     * @param bool   $toLower
     * @param bool   $addSlashes
     * @param bool   $removeAccents
     * @return string
     */
    public static function clean(
        string $string,
        bool $toLower = false,
        bool $addSlashes = false,
        bool $removeAccents = true
    ): string {
        if ($removeAccents) {
            $string = Slug::removeAccents($string);
        }

        $string = strip_tags($string);
        $string = trim($string);

        if ($addSlashes) {
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
    public static function htmlEnt(string $string, bool $encodedEntities = false): string
    {
        if ($encodedEntities) {
            $transTable = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES, self::$encoding);

            $transTable[chr(38)] = '&';

            $regExp = '/&(?![A-Za-z]{0,4}\w{2,3};|#[\d]{2,3};)/';

            return (string)preg_replace($regExp, '&amp;', strtr($string, $transTable));
        }

        return (string)htmlentities($string, ENT_QUOTES, self::$encoding);
    }

    /**
     * Get unique string
     *
     * @param string $prefix
     * @return string
     * @throws \Exception
     */
    public static function unique(string $prefix = 'unique'): string
    {
        $prefix = rtrim(trim($prefix), '-');
        $random = random_int(10000000, 99999999);

        $result = $random;
        if ($prefix) {
            $result = $prefix . '-' . $random;
        }

        return (string)$result;
    }

    /**
     * Generate readable random string
     *
     * @param int  $length
     * @param bool $isReadable
     * @return string
     */
    public static function random(int $length = 10, bool $isReadable = true): string
    {
        $result = '';

        if ($isReadable) {
            $vowels = ['a', 'e', 'i', 'o', 'u', '0'];
            $consonants = [
                'b',
                'c',
                'd',
                'f',
                'g',
                'h',
                'j',
                'k',
                'l',
                'm',
                'n',
                'p',
                'r',
                's',
                't',
                'v',
                'w',
                'x',
                'y',
                'z',
                '1',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9',
            ];

            $max = $length / 2;

            for ($pos = 1; $pos <= $max; $pos++) {
                $result .= $consonants[random_int(0, count($consonants) - 1)];
                $result .= $vowels[random_int(0, count($vowels) - 1)];
            }
        } else {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            for ($pos = 0; $pos < $length; $pos++) {
                $result .= $chars[mt_rand() % strlen($chars)];
            }
        }

        return $result;
    }

    /**
     * Pads a given string with zeroes on the left.
     *
     * @param string $number The number to pad
     * @param int    $length The total length of the desired string
     * @return string
     */
    public static function zeroPad(string $number, int $length): string
    {
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Truncate a string to a specified length without cutting a word off.
     *
     * @param string $string The string to truncate
     * @param int    $length The length to truncate the string to
     * @param string $append Text to append to the string IF it gets truncated, defaults to '...'
     * @return  string
     */
    public static function truncateSafe(string $string, int $length, string $append = '...'): string
    {
        $result = self::sub($string, 0, $length);
        $lastSpace = self::rPos($result, ' ');

        if ($lastSpace !== null && $string !== $result) {
            $result = self::sub($result, 0, $lastSpace);
        }

        if ($result !== $string) {
            $result .= $append;
        }

        return $result;
    }

    /**
     * Truncate the string to given length of characters.
     *
     * @param string $string The variable to truncate
     * @param int    $limit  The length to truncate the string to
     * @param string $append Text to append to the string IF it gets truncated, defaults to '...'
     * @return string
     */
    public static function limitChars(string $string, int $limit = 100, string $append = '...'): string
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
    public static function limitWords(string $string, int $limit = 100, string $append = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);

        if (!array_key_exists('0', $matches) || self::len($string) === self::len($matches[0])) {
            return $string;
        }

        return rtrim($matches[0]) . $append;
    }

    /**
     * Check if a given string matches a given pattern.
     *
     * @param string $pattern  Pattern of string expected
     * @param string $haystack String that need to be matched
     * @param bool   $caseSensitive
     * @return bool
     */
    public static function like(string $pattern, string $haystack, bool $caseSensitive = true): bool
    {
        if ($pattern === $haystack) {
            return true;
        }

        // Preg flags
        $flags = $caseSensitive ? '' : 'i';

        // Escape any regex special characters
        $pattern = preg_quote($pattern, '#');

        // Unescaped * which is our wildcard character and change it to .*
        $pattern = str_replace('\*', '.*', $pattern);

        return (bool)preg_match('#^' . $pattern . '$#' . $flags, $haystack);
    }

    /**
     * Converts any accent characters to their equivalent normal characters
     *
     * @param string $text
     * @param bool   $isCache
     * @return string
     */
    public static function slug(string $text = '', bool $isCache = false): string
    {
        static $cache = [];

        if (!$isCache) {
            return Slug::filter($text);
        }

        if (!array_key_exists($text, $cache)) { // Not Arr::key() for performance
            $cache[$text] = Slug::filter($text);
        }

        return $cache[$text];
    }

    /**
     * Check is mbstring overload standard functions
     * @return bool
     */
    public static function isOverload(): bool
    {
        if (defined('MB_OVERLOAD_STRING') && self::isMBString()) {
            return (bool)(Filter::int(Sys::iniGet('mbstring.func_overload')) & MB_OVERLOAD_STRING);
        }

        return false;
    }

    /**
     * Check is mbstring loaded
     *
     * @return bool
     */
    public static function isMBString(): bool
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
     * @param string $string
     * @return int
     */
    public static function len(string $string): int
    {
        if (self::isMBString()) {
            return (int)mb_strlen($string, self::$encoding);
        }

        return (int)strlen($string);
    }

    /**
     * Find position of first occurrence of string in a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @return int|null
     */
    public static function pos(string $haystack, string $needle, int $offset = 0): ?int
    {
        $result = strpos($haystack, $needle, $offset);
        if (self::isMBString()) {
            $result = mb_strpos($haystack, $needle, $offset, self::$encoding);
        }

        return $result === false ? null : $result;
    }

    /**
     * Find position of last occurrence of a string in a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @return int|null
     */
    public static function rPos(string $haystack, string $needle, int $offset = 0): ?int
    {
        $result = strrpos($haystack, $needle, $offset);
        if (self::isMBString()) {
            $result = mb_strrpos($haystack, $needle, $offset, self::$encoding);
        }

        return $result === false ? null : $result;
    }

    /**
     * Finds position of first occurrence of a string within another, case insensitive
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @return int|null
     */
    public static function iPos(string $haystack, string $needle, int $offset = 0): ?int
    {
        $result = (int)stripos($haystack, $needle, $offset);
        if (self::isMBString()) {
            $result = mb_stripos($haystack, $needle, $offset, self::$encoding);
        }

        return $result === false ? null : $result;
    }

    /**
     * Finds first occurrence of a string within another
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $beforeNeedle
     * @return string
     */
    public static function strStr(string $haystack, string $needle, bool $beforeNeedle = false): string
    {
        if (self::isMBString()) {
            return (string)mb_strstr($haystack, $needle, $beforeNeedle, self::$encoding);
        }

        return (string)strstr($haystack, $needle, $beforeNeedle);
    }

    /**
     * Finds first occurrence of a string within another, case insensitive
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $beforeNeedle
     * @return string
     */
    public static function iStr(string $haystack, string $needle, bool $beforeNeedle = false): string
    {
        if (self::isMBString()) {
            return (string)mb_stristr($haystack, $needle, $beforeNeedle, self::$encoding);
        }

        return (string)stristr($haystack, $needle, $beforeNeedle);
    }

    /**
     * Finds the last occurrence of a character in a string within another
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $part
     * @return string
     */
    public static function rChr(string $haystack, string $needle, bool $part = false): string
    {
        if (self::isMBString()) {
            return (string)mb_strrchr($haystack, $needle, $part, self::$encoding);
        }

        return (string)strrchr($haystack, $needle);
    }

    /**
     * Get part of string
     *
     * @param string $string
     * @param int    $start
     * @param int    $length
     * @return string
     */
    public static function sub(string $string, int $start, int $length = 0): string
    {
        if (self::isMBString()) {
            if (0 === $length) {
                $length = self::len($string);
            }

            return (string)mb_substr($string, $start, $length, self::$encoding);
        }

        return (string)substr($string, $start, $length);
    }

    /**
     * Make a string lowercase
     *
     * @param string|float|int|bool|null $string
     * @return string
     */
    public static function low($string): string
    {
        if (self::isMBString()) {
            return (string)mb_strtolower((string)$string, self::$encoding);
        }

        return (string)strtolower((string)$string);
    }

    /**
     * Make a string uppercase
     *
     * @param string|float|int|bool|null $string
     * @return string
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function up($string): string
    {
        if (self::isMBString()) {
            return (string)mb_strtoupper((string)$string, self::$encoding);
        }

        return (string)strtoupper((string)$string);
    }

    /**
     * Count the number of substring occurrences
     *
     * @param string $haystack
     * @param string $needle
     * @return int
     */
    public static function subCount(string $haystack, string $needle): int
    {
        if (self::isMBString()) {
            return (int)mb_substr_count($haystack, $needle, self::$encoding);
        }

        return (int)substr_count($haystack, $needle);
    }

    /**
     * Checks if the $haystack starts with the text in the $needle.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $caseSensitive
     * @return bool
     */
    public static function isStart(string $haystack, string $needle, bool $caseSensitive = false): bool
    {
        if ($caseSensitive) {
            return $needle === '' || self::pos($haystack, $needle) === 0;
        }

        return $needle === '' || self::iPos($haystack, $needle) === 0;
    }

    /**
     * Checks if the $haystack ends with the text in the $needle. Case sensitive.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $caseSensitive
     * @return bool
     */
    public static function isEnd(string $haystack, string $needle, bool $caseSensitive = false): bool
    {
        if ($caseSensitive) {
            return $needle === '' || self::sub($haystack, -self::len($needle)) === $needle;
        }

        $haystack = self::low($haystack);
        $needle = self::low($needle);

        return $needle === '' || self::sub($haystack, -self::len($needle)) === $needle;
    }

    /**
     * Trim whitespaces and other special chars
     *
     * @param string $value
     * @param bool   $extendMode
     * @return string
     */
    public static function trim(string $value, bool $extendMode = false): string
    {
        $result = trim($value);

        if ($extendMode) {
            $result = trim($result, chr(0xE3) . chr(0x80) . chr(0x80));
            $result = trim($result, chr(0xC2) . chr(0xA0));
            $result = trim($result);
        }

        return $result;
    }

    /**
     * Escape string before save it as xml content.
     * The function is moved. Please, use \JBZoo\Utils\Xml::escape($string). It'll be deprecated soon.
     *
     * @param string $string
     * @return string
     * @deprecated
     */
    public static function escXml(string $string): string
    {
        return Xml::escape($string);
    }

    /**
     * Escape UTF-8 strings
     *
     * @param string $string
     * @return string
     */
    public static function esc(string $string): string
    {
        return htmlspecialchars($string, ENT_NOQUOTES, self::$encoding);
    }

    /**
     * Convert camel case to human readable format
     *
     * @param string $input
     * @param string $separator
     * @param bool   $toLower
     * @return string
     */
    public static function splitCamelCase(string $input, string $separator = '_', bool $toLower = true): string
    {
        $original = $input;

        $output = (string)preg_replace(['/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'], '_$0', $input);
        $output = (string)preg_replace('#_{1,}#', $separator, $output);

        $output = trim($output);
        if ($toLower) {
            $output = strtolower($output);
        }

        if ('' === $output) {
            return $original;
        }

        return $output;
    }

    /**
     * Convert test name to human readable string
     *
     * @param string $input
     * @return string
     */
    public static function testName2Human(string $input): string
    {
        $original = $input;
        $input = self::getClassName($input);

        /** @noinspection NotOptimalRegularExpressionsInspection */
        if (!preg_match('#^tests#i', $input)) {
            $input = (string)preg_replace('#^(test)#i', '', $input);
        }

        $input = (string)preg_replace('#(test)$#i', '', $input);
        $output = (string)preg_replace(['/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'], ' $0', $input);
        $output = str_replace('_', ' ', $output);
        $output = trim($output);

        $output = implode(' ', array_filter(array_map(static function (string $item): string {
            $item = ucwords($item);
            $item = trim($item);
            return $item;
        }, explode(' ', $output))));


        if (strcasecmp($original, $output) === 0) {
            return $original;
        }

        if ('' === $output) {
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
    public static function uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            // 16 bits for "time_mid"
            random_int(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            random_int(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            random_int(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff)
        );
    }

    /**
     * Get class name without namespace
     *
     * @param mixed $object
     * @param bool  $toLower
     * @return string
     */
    public static function getClassName($object, bool $toLower = false): string
    {
        $className = $object;
        if (is_object($object)) {
            $className = get_class($object);
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
     * @param string $string The source string.
     * @param string $style  The the style (default|dash).
     * @param int    $next   If supplied, this number is used for the copy, otherwise it is the 'next' number.
     * @return string
     */
    public static function inc(string $string, string $style = 'default', int $next = 0): string
    {
        $styles = [
            'dash'    => ['#-(\d+)$#', '-%d'],
            'default' => [['#\((\d+)\)$#', '#\(\d+\)$#'], [' (%d)', '(%d)']],
        ];

        $styleSpec = $styles[$style] ?? $styles['default'];

        // Regular expression search and replace patterns.
        if (is_array($styleSpec[0])) {
            $rxSearch = $styleSpec[0][0];
            /** @noinspection MultiAssignmentUsageInspection */
            $rxReplace = $styleSpec[0][1];
        } else {
            $rxSearch = $rxReplace = $styleSpec[0];
        }

        // New and old (existing) sprintf formats.
        if (is_array($styleSpec[1])) {
            $newFormat = $styleSpec[1][0];
            /** @noinspection MultiAssignmentUsageInspection */
            $oldFormat = $styleSpec[1][1];
        } else {
            $newFormat = $oldFormat = $styleSpec[1];
        }

        // Check if we are incrementing an existing pattern, or appending a new one.
        if (preg_match($rxSearch, $string, $matches)) {
            $next = empty($next) ? ((int)$matches[1] + 1) : $next;
            $string = (string)preg_replace($rxReplace, sprintf($oldFormat, $next), $string);
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
     * @param string $sql Input SQL string with which to split into individual queries.
     * @return array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function splitSql(string $sql): array
    {
        $start = 0;
        $open = false;
        $comment = false;
        $endString = '';
        $end = strlen($sql);
        $queries = [];
        $query = '';

        for ($i = 0; $i < $end; $i++) {
            $current = $sql[$i];
            $current2 = substr($sql, $i, 2);
            $current3 = substr($sql, $i, 3);
            $lenEndString = strlen($endString);
            $testEnd = substr($sql, $i, $lenEndString);

            $quotedWithBackslash = $current === '"' || $current === "'" || $current2 === '--' ||
                ($current2 === '/*' && $current3 !== '/*!' && $current3 !== '/*+') ||
                ($current === '#' && $current3 !== '#__') ||
                ($comment && $testEnd === $endString);

            if ($quotedWithBackslash) {
                // Check if quoted with previous backslash
                $num = 2;

                while ($sql[$i - $num + 1] === '\\' && $num < $i) {
                    $num++;
                }

                // Not quoted
                if ($num % 2 === 0) {
                    if ($open) {
                        if ($testEnd === $endString) {
                            if ($comment) {
                                $comment = false;
                                if ($lenEndString > 1) {
                                    $i += ($lenEndString - 1);
                                    $current = $sql[$i];
                                }
                                $start = $i + 1;
                            }
                            $open = false;
                            $endString = '';
                        }
                    } else {
                        $open = true;
                        if ('--' === $current2) {
                            $endString = "\n";
                            $comment = true;
                        } elseif ('/*' === $current2) {
                            $endString = '*/';
                            $comment = true;
                        } elseif ('#' === $current) {
                            $endString = "\n";
                            $comment = true;
                        } else {
                            $endString = $current;
                        }
                        if ($comment && $start < $i) {
                            $query .= substr($sql, $start, (int)($i - $start));
                        }
                    }
                }
            }

            if ($comment) {
                $start = $i + 1;
            }

            if (($current === ';' && !$open) || $i === $end - 1) {
                if ($start <= $i) {
                    $query .= substr($sql, $start, $i - $start + 1);
                }
                $query = trim($query);

                if ($query) {
                    if ($current !== ';') {
                        $query .= ';';
                    }
                    $queries[] = $query;
                }

                $query = '';
                $start = $i + 1;
            }
        }

        return $queries;
    }

    /**
     * Convert array of strings to list as pretty print description
     * @param array $data
     * @param bool  $alignByKeys
     * @return string|null
     */
    public static function listToDescription(array $data, bool $alignByKeys = false): ?string
    {
        /** @psalm-suppress MissingClosureParamType */
        $maxWidth = array_reduce(array_keys($data), static function ($acc, $key) use ($data): int {
            if ('' === trim($data[$key])) {
                return $acc;
            }

            if ($acc < strlen($key)) {
                $acc = strlen($key);
            }

            return $acc;
        }, 0);

        $result = [];
        foreach ($data as $key => $value) {
            $value = trim($value);
            $key = trim($key);

            if ('' !== $value) {
                $keyFormated = $key;
                if ($alignByKeys) {
                    $keyFormated = str_pad($key, $maxWidth, ' ', STR_PAD_RIGHT);
                }

                if (is_numeric($key) || $key === '') {
                    $result[] = $value;
                } else {
                    $result[] = ucfirst($keyFormated) . ': ' . $value;
                }
            }
        }

        if (count($result) === 0) {
            return null;
        }

        return implode("\n", $result) . "\n";
    }
}
