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
     * @return boolean
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
     * @param string|float|int|null $value
     * @param int                   $round
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
     * @param string|int|float $value
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
     * Return only digits chars
     *
     * @param string $value
     * @return string
     */
    public static function digits($value)
    {
        // we need to remove - and + because they're allowed in the filter
        $cleaned = str_replace(['-', '+'], '', $value);
        $cleaned = (string)filter_var($cleaned, FILTER_SANITIZE_NUMBER_INT);

        return $cleaned;
    }

    /**
     * Return only alpha chars
     *
     * @param string $value
     * @return mixed
     */
    public static function alpha($value)
    {
        return (string)preg_replace('#[^[:alpha:]]#', '', $value);
    }

    /**
     * Return only alpha and digits chars
     *
     * @param string $value
     * @return mixed
     */
    public static function alphanum($value)
    {
        return (string)preg_replace('#[^[:alnum:]]#', '', $value);
    }

    /**
     * Return only chars for base64
     *
     * @param string $value
     * @return string
     */
    public static function base64($value): string
    {
        return (string)preg_replace('#[^A-Z0-9\/+=]#i', '', $value);
    }

    /**
     * Remove whitespaces
     *
     * @param string $value
     * @return string
     */
    public static function path($value): string
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
    public static function trim($value): string
    {
        return Str::trim($value);
    }

    /**
     * Remove whitespaces
     *
     * @param string $value
     * @return string
     */
    public static function trimExtend($value): string
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
    public static function cmd($value): string
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
     * @return mixed
     */
    public static function strip($string)
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
    public static function alias($string): string
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
    public static function low($string): string
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
    public static function up($string): string
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
    public static function stripSpace($string): string
    {
        return Str::stripSpace($string);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function clean($string): string
    {
        return Str::clean($string, true, true);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function html($string): string
    {
        return Str::htmlEnt($string);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function xml($string): string
    {
        return Str::escXml($string);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function esc($string): string
    {
        return Str::esc($string);
    }

    /**
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
     * @param string $string
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
    public static function ucFirst($input): string
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
    public static function className($input): string
    {
        $output = (string)preg_replace(['#(?<=[^A-Z\s])([A-Z\s])#i'], ' $0', $input);
        $output = explode(' ', $output);

        $output = array_map(function ($item) {
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
    public static function stripQuotes($value): string
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
