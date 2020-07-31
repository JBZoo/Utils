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

use Closure;
use JBZoo\Data\Data;
use JBZoo\Data\JSON;

/**
 * Class Filter
 *
 * @package JBZoo\Utils
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Filter
{
    /**
     * Apply custom filter to variable
     *
     * @param mixed          $value
     * @param string|Closure $filters
     * @return mixed
     * @throws Exception
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function _($value, $filters = 'raw')
    {
        if (is_string($filters)) {
            $filters = Str::trim($filters);
            $filters = (array)explode(',', $filters);

            foreach ($filters as $filter) {
                $filterName = self::cmd($filter);

                if ($filterName) {
                    if (method_exists(__CLASS__, $filterName)) {
                        $value = self::$filterName($value);
                    } else {
                        throw new Exception('Undefined filter method: ' . $filter);
                    }
                }
            }
        } else {
            $value = $filters($value);
        }

        return $value;
    }

    /**
     * Converts many english words that equate to true or false to boolean.
     *
     * @param mixed $variable The string to convert to boolean
     * @return bool
     */
    public static function bool($variable): bool
    {
        $yesList = [
            'affirmative',
            'all right',
            'aye',
            'indubitably',
            'most assuredly',
            'ok',
            'of course',
            'oui',
            'okay',
            'sure thing',
            'y',
            'yes',
            'yea',
            'yep',
            'sure',
            'yeah',
            'true',
            't',
            'on',
            '1',
            'vrai',
            'да',
            'д',
            '+',
            '++',
            '+++',
            '++++',
            '+++++',
            '*',
        ];

        $noList = [
            'no*',
            'no way',
            'nope',
            'nah',
            'na',
            'never',
            'absolutely not',
            'by no means',
            'negative',
            'never ever',
            'false',
            'f',
            'off',
            '0',
            'non',
            'faux',
            'нет',
            'н',
            'немає',
            '-',
            'null',
            'nill',
            'undefined',
        ];

        $variable = Str::low($variable);

        if (Arr::in($variable, $yesList) || self::float($variable) !== 0.0) {
            return true;
        }

        if (Arr::in($variable, $noList)) {
            return false;
        }

        return filter_var($variable, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Smart converter string to float
     *
     * @param mixed $value
     * @param int   $round
     * @return float
     */
    public static function float($value, int $round = 10): float
    {
        $cleaned = (string)preg_replace('#[^\deE\-\.\,]#iu', '', (string)$value);
        $cleaned = str_replace(',', '.', $cleaned);

        preg_match('#[-+]?[\d]+(\.[\d]+)?([eE][-+]?[\d]+)?#', $cleaned, $matches);
        $result = (float)($matches[0] ?? 0.0);

        $result = round($result, $round);

        return $result;
    }

    /**
     * Smart convert any string to int
     *
     * @param string|float|int|null $value
     * @return int
     */
    public static function int($value): int
    {
        $cleaned = (string)preg_replace('#[^0-9-+.,]#', '', (string)$value);
        preg_match('#[-+]?[\d]+#', $cleaned, $matches);
        $result = $matches[0] ?? 0;

        return (int)$result;
    }

    /**
     * Returns only digits chars
     *
     * @param string|null $value
     * @return string
     */
    public static function digits(?string $value): string
    {
        // we need to remove - and + because they're allowed in the filter
        $cleaned = str_replace(['-', '+'], '', (string)$value);
        $cleaned = (string)filter_var($cleaned, FILTER_SANITIZE_NUMBER_INT);

        return $cleaned;
    }

    /**
     * Returns only alpha chars
     *
     * @param string|null $value
     * @return string
     */
    public static function alpha(?string $value): string
    {
        return (string)preg_replace('#[^[:alpha:]]#', '', (string)$value);
    }

    /**
     * Returns only alpha and digits chars
     *
     * @param string|null $value
     * @return string
     */
    public static function alphanum(?string $value): string
    {
        return (string)preg_replace('#[^[:alnum:]]#', '', (string)$value);
    }

    /**
     * Returns only chars for base64
     *
     * @param string $value
     * @return string
     */
    public static function base64(string $value): string
    {
        return (string)preg_replace('#[^A-Z0-9\/+=]#i', '', $value);
    }

    /**
     * Remove whitespaces
     *
     * @param string $value
     * @return string
     */
    public static function path(string $value): string
    {
        $pattern = '#^[A-Za-z0-9_\/-]+[A-Za-z0-9_\.-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-]*)*$#';
        preg_match($pattern, $value, $matches);
        return isset($matches[0]) ? (string)$matches[0] : '';
    }

    /**
     * Remove whitespaces
     *
     * @param string $value
     * @return string
     */
    public static function trim(string $value): string
    {
        return Str::trim($value);
    }

    /**
     * Remove whitespaces
     *
     * @param string $value
     * @return string
     */
    public static function trimExtend(string $value): string
    {
        return Str::trim($value, true);
    }

    /**
     * Cleanup array
     *
     * @param mixed          $value
     * @param string|Closure $filter
     * @return array
     */
    public static function arr($value, $filter = null): array
    {
        $array = (array)$value;

        if ($filter === 'noempty') {
            $array = Arr::clean($array);
        } elseif ($filter instanceof Closure) {
            $array = array_filter($array, $filter); // TODO add support both - key + value
        }

        return $array;
    }

    /**
     * Cleanup system command
     *
     * @param string $value
     * @return string
     */
    public static function cmd(string $value): string
    {
        $value = Str::low($value);
        $value = (string)preg_replace('#[^a-z0-9\_\-\.]#', '', $value);
        $value = Str::trim($value);

        return $value;
    }

    /**
     * Get safe string
     *
     * @param string $string
     * @return string
     */
    public static function strip(string $string): string
    {
        $cleaned = strip_tags($string);
        $cleaned = Str::trim($cleaned);

        return $cleaned;
    }

    /**
     * Get safe string
     *
     * @param string $string
     * @return string
     */
    public static function alias(string $string): string
    {
        $cleaned = self::strip($string);
        $cleaned = Str::slug($cleaned);

        return $cleaned;
    }

    /**
     * String to lower and trim
     *
     * @param string $string
     * @return string
     */
    public static function low(string $string): string
    {
        $cleaned = Str::low($string);
        $cleaned = Str::trim($cleaned);

        return $cleaned;
    }

    /**
     * String to upper and trim
     *
     * @param string $string
     * @return string
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function up(string $string): string
    {
        $cleaned = Str::up($string);
        $cleaned = Str::trim($cleaned);

        return $cleaned;
    }

    /**
     * Strip spaces
     *
     * @param string $string
     * @return string
     */
    public static function stripSpace(string $string): string
    {
        return Str::stripSpace($string);
    }

    /**
     * Alias of "Str::clean($string, true, true)"
     *
     * @param string $string
     * @return string
     */
    public static function clean(string $string): string
    {
        return Str::clean($string, true, true);
    }

    /**
     * Alias of "Str::htmlEnt($string)"
     *
     * @param string $string
     * @return string
     */
    public static function html(string $string): string
    {
        return Str::htmlEnt($string);
    }

    /**
     * Alias of "Xml::escape($string)"
     *
     * @param string $string
     * @return string
     */
    public static function xml(string $string): string
    {
        return Xml::escape($string);
    }

    /**
     * Alias of "Str::esc($string)"
     *
     * @param string $string
     * @return string
     */
    public static function esc(string $string): string
    {
        return Str::esc($string);
    }

    /**
     * Returns JSON object from array
     *
     * @param array|Data $data
     * @return Data
     */
    public static function data($data): Data
    {
        if ($data instanceof Data) {
            return $data;
        }

        return new JSON($data);
    }

    /**
     * RAW placeholder
     *
     * @param mixed $string
     * @return mixed
     */
    public static function raw($string)
    {
        return $string;
    }

    /**
     * First char to upper, other to lower
     *
     * @param string $input
     * @return string
     */
    public static function ucFirst(string $input): string
    {
        $string = Str::low($input);
        $string = ucfirst($string);

        return $string;
    }

    /**
     * Parse lines to assoc list
     *
     * @param string|array $input
     * @return array
     */
    public static function parseLines($input): array
    {
        if (is_array($input)) {
            $input = implode(PHP_EOL, $input);
        }

        return Str::parseLines($input);
    }

    /**
     * Convert words to PHP Class name
     *
     * @param string $input
     * @return string
     */
    public static function className(string $input): string
    {
        $output = (string)preg_replace(['#(?<=[^A-Z\s])([A-Z\s])#i'], ' $0', $input);
        $output = explode(' ', $output);

        $output = array_map(static function ($item) {
            $item = (string)preg_replace('#[^a-z0-9]#i', '', $item);
            $item = Filter::ucFirst($item);
            return $item;
        }, $output);

        $output = array_filter($output);

        return implode('', $output);
    }

    /**
     * Strip quotes.
     *
     * @param string $value
     * @return string
     */
    public static function stripQuotes(string $value): string
    {
        if (strpos($value, '"') === 0 && substr($value, -1) === '"') {
            $value = trim($value, '"');
        }

        if (strpos($value, "'") === 0 && substr($value, -1) === "'") {
            $value = trim($value, "'");
        }

        return $value;
    }
}
