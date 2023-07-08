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

use JBZoo\Data\Data;
use JBZoo\Data\JSON;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class Filter
{
    /**
     * Apply custom filter to variable.
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function _(mixed $value, \Closure|string $filters = 'raw'): mixed
    {
        if (\is_string($filters)) {
            $filters = Str::trim($filters);
            $filters = \explode(',', $filters);

            foreach ($filters as $filter) {
                $filterName = self::cmd($filter);

                if (!isStrEmpty($filterName)) {
                    if (\method_exists(self::class, $filterName)) {
                        /** @phpstan-ignore-next-line */
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
     */
    public static function bool(mixed $variable): bool
    {
        if ($variable === null) {
            return false;
        }

        if (\is_bool($variable)) {
            return $variable;
        }

        if (\is_array($variable)) {
            return \count($variable) > 0;
        }

        if (
            !\is_float($variable)
            && !\is_int($variable)
            && !\is_numeric($variable)
            && !\is_string($variable)
        ) {
            // @phpstan-ignore-next-line
            return empty($variable);
        }

        $yesList = [
            '*',
            '+',
            '++',
            '+++',
            '++++',
            '+++++',
            '1',
            'affirmative',
            'all right',
            'aye',
            'indubitably',
            'most assuredly',
            'of course',
            'ok',
            'okay',
            'on',
            'oui',
            'sure thing',
            'sure',
            't',
            'true',
            'vrai',
            'y',
            'yea',
            'yeah',
            'yep',
            'yes',
            'д',
            'да',
        ];

        $noList = [
            '0',
            '-',
            'absolutely not',
            'by no means',
            'f',
            'false',
            'faux',
            'na',
            'nah',
            'negative',
            'never ever',
            'never',
            'nil',
            'nill',
            'no way',
            'no*',
            'non',
            'nope',
            'null',
            'off',
            'undefined',
            'н',
            'немає',
            'нет',
        ];

        $variable = Str::low((string)$variable);

        if (\in_array($variable, $yesList, true) || self::float($variable) !== 0.0) {
            return true;
        }

        if (\in_array($variable, $noList, true)) {
            return false;
        }

        return \filter_var($variable, \FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Smart converter string to float.
     */
    public static function float(mixed $value, int $round = 10): float
    {
        $cleaned = (string)\preg_replace('#[^\deE\-\.\,]#iu', '', (string)$value);
        $cleaned = \str_replace(',', '.', $cleaned);

        \preg_match('#[-+]?[\d]+(\.[\d]+)?([eE][-+]?[\d]+)?#', $cleaned, $matches);
        $result = (float)($matches[0] ?? 0.0);

        return \round($result, $round);
    }

    /**
     * Smart convert any string to int.
     */
    public static function int(float|bool|int|string|null $value): int
    {
        $cleaned = (string)\preg_replace('#[^0-9-+.,]#', '', (string)$value);
        \preg_match('#[-+]?[\d]+#', $cleaned, $matches);
        $result = $matches[0] ?? 0;

        return (int)$result;
    }

    /**
     * Returns only digits chars.
     */
    public static function digits(?string $value): string
    {
        // we need to remove - and + because they're allowed in the filter
        $cleaned = \str_replace(['-', '+'], '', (string)$value);

        return (string)\filter_var($cleaned, \FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Returns only alpha chars.
     */
    public static function alpha(?string $value): string
    {
        return (string)\preg_replace('#[^[:alpha:]]#', '', (string)$value);
    }

    /**
     * Returns only alpha and digits chars.
     */
    public static function alphanum(?string $value): string
    {
        return (string)\preg_replace('#[^[:alnum:]]#', '', (string)$value);
    }

    /**
     * Returns only chars for base64.
     */
    public static function base64(string $value): string
    {
        return (string)\preg_replace('#[^A-Z0-9\/+=]#i', '', $value);
    }

    /**
     * Returns only chars for base64url.
     */
    public static function path(string $value): string
    {
        $pattern = '#^[A-Za-z0-9_\/-]+[A-Za-z0-9_\.-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-]*)*$#';
        \preg_match($pattern, $value, $matches);

        return $matches[0] ?? '';
    }

    /**
     * Alias for build-in function \trim().
     */
    public static function trim(string $value): string
    {
        return Str::trim($value);
    }

    /**
     * Extended trim function for remove all spaces, tabs, new lines and really special chars.
     */
    public static function trimExtend(string $value): string
    {
        return Str::trim($value, true);
    }

    /**
     * Cleanup array. No empty values.
     */
    public static function arr(mixed $value, string|\Closure|null $filter = null): array
    {
        $array = (array)$value;

        if ($filter === 'noempty') {
            $array = Arr::clean($array);
        } elseif ($filter instanceof \Closure) {
            $array = \array_filter($array, $filter); // TODO add support both - key + value
        }

        return $array;
    }

    /**
     * Cleanup system command.
     */
    public static function cmd(string $value): string
    {
        $value = Str::low($value);
        $value = (string)\preg_replace('#[^a-z0-9\_\-\.]#', '', $value);

        return Str::trim($value);
    }

    /**
     * Get safe string without html tags and trimmed.
     */
    public static function strip(string $string): string
    {
        $cleaned = \strip_tags($string);

        return Str::trim($cleaned);
    }

    /**
     * Get safe string for sensitive external dependencies.
     */
    public static function alias(string $string): string
    {
        $cleaned = self::strip($string);

        return Str::slug($cleaned);
    }

    /**
     * String to lower and trim.
     */
    public static function low(string $string): string
    {
        $cleaned = Str::low($string);

        return Str::trim($cleaned);
    }

    /**
     * String to upper and trim.
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function up(string $string): string
    {
        $cleaned = Str::up($string);

        return Str::trim($cleaned);
    }

    /**
     * Alias of "Str::stripSpace($string)".
     */
    public static function stripSpace(string $string): string
    {
        return Str::stripSpace($string);
    }

    /**
     * Alias of "Str::clean($string, true, true)".
     */
    public static function clean(string $string): string
    {
        return Str::clean($string, true, true);
    }

    /**
     * Alias of "Str::htmlEnt($string)".
     */
    public static function html(string $string): string
    {
        return Str::htmlEnt($string);
    }

    /**
     * Alias of "Xml::escape($string)".
     */
    public static function xml(string $string): string
    {
        return Xml::escape($string);
    }

    /**
     * Alias of "Str::esc($string)".
     */
    public static function esc(string $string): string
    {
        return Str::esc($string);
    }

    /**
     * Returns Data object from array.
     */
    public static function data(Data|array $data): Data
    {
        if ($data instanceof Data) {
            return $data;
        }

        return new Data($data);
    }

    /**
     * Returns JSON object from array.
     */
    public static function json(JSON|array $data): JSON
    {
        if ($data instanceof JSON) {
            return $data;
        }

        return new JSON($data);
    }

    /**
     * RAW placeholder for internal API of the library.
     */
    public static function raw(mixed $variable): mixed
    {
        return $variable;
    }

    /**
     * First char to upper, other to lower.
     */
    public static function ucFirst(string $input): string
    {
        $string = Str::low($input);

        return \ucfirst($string);
    }

    /**
     * Parse lines to assoc list.
     */
    public static function parseLines(array|string $input): array
    {
        if (\is_array($input)) {
            $input = \implode(\PHP_EOL, $input);
        }

        return Str::parseLines($input);
    }

    /**
     * Convert words to PHP Class name.
     */
    public static function className(string $input): string
    {
        $output = (string)\preg_replace(['#(?<=[^A-Z\s])([A-Z\s])#i'], ' $0', $input);
        $output = \explode(' ', $output);

        $output = \array_map(static function ($item) {
            $item = (string)\preg_replace('#[^a-z0-9]#i', '', $item);

            return self::ucFirst($item);
        }, $output);

        $output = \array_filter($output);

        return \implode('', $output);
    }

    /**
     * Smart striping quotes, double and single.
     */
    public static function stripQuotes(string $value): string
    {
        if (\str_starts_with($value, '"') && \str_ends_with($value, '"')) {
            $value = \trim($value, '"');
        }

        if (\str_starts_with($value, "'") && \str_ends_with($value, "'")) {
            $value = \trim($value, "'");
        }

        return $value;
    }
}
