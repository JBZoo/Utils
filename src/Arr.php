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

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class Arr
{
    /**
     * Remove the duplicates from an array.
     */
    public static function unique(array $array, bool $keepKeys = false): array
    {
        if ($keepKeys) {
            // This is faster than the builtin array_unique().
            $uniqueArray = [];

            foreach ($array as $key => $value) {
                if (isset($uniqueArray[$value])) {
                    continue;
                }

                $uniqueArray[$value] = $key;
            }
            
            return \array_flip($uniqueArray);
        }

        // This is faster version than the builtin array_unique().
        // http://stackoverflow.com/questions/8321620/array-unique-vs-array-flip
        // http://php.net/manual/en/function.array-unique.php
        return \array_keys(\array_flip($array));
    }

    /**
     * Check if key exists.
     * @deprecated Use array_key_exists or ?: or ??
     */
    public static function key(mixed $key, array $array, bool $returnValue = false): mixed
    {
        $isExists = \array_key_exists((string)$key, $array);

        if ($returnValue) {
            if ($isExists) {
                return $array[$key];
            }

            return null;
        }

        return $isExists;
    }

    /**
     * Check is value exists in the array.
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function in(mixed $value, array $array, bool $returnKey = false): null|bool|int|string
    {
        $inArray = \in_array($value, $array, true);

        if ($returnKey) {
            if ($inArray) {
                return \array_search($value, $array, true);
            }

            return null;
        }

        return $inArray;
    }

    /**
     * Returns the first element in an array.
     */
    public static function first(array $array): mixed
    {
        return \reset($array);
    }

    /**
     * Returns the last element in an array.
     */
    public static function last(array $array): mixed
    {
        return \end($array);
    }

    /**
     * Returns the first key in an array.
     */
    public static function firstKey(array $array): null|int|string
    {
        \reset($array);

        return \key($array);
    }

    /**
     * Returns the last key in an array.
     */
    public static function lastKey(array $array): null|int|string
    {
        \end($array);

        return \key($array);
    }

    /**
     * Flatten a multi-dimensional array into a one dimensional array.
     * @param array $array        The array to flatten
     * @param bool  $preserveKeys Whether to preserve array keys. Keys from deeply nested arrays will
     *                            overwrite keys from shallow nested arrays
     */
    public static function flat(array $array, bool $preserveKeys = true): array
    {
        $flattened = [];

        \array_walk_recursive(
            $array,
            static function (mixed $value, int|string $key) use (&$flattened, $preserveKeys): void {
                if ($preserveKeys && !\is_int($key)) {
                    $flattened[$key] = $value;
                } else {
                    $flattened[] = $value;
                }
            },
        );

        return $flattened;
    }

    /**
     * Searches for a given value in an array of arrays, objects and scalar values. You can optionally specify
     * a field of the nested arrays and objects to search in.
     * @param  array                      $array  The array to search
     * @param  null|bool|float|int|string $search The value to search for
     * @param  null|string                $field  The field to search in, if not specified all fields will be searched
     * @return bool|string                False on failure or the array key on success
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function search(
        array $array,
        null|bool|float|int|string $search,
        ?string $field = null,
    ): bool|string {
        // *grumbles* stupid PHP type system
        $search = (string)$search;

        foreach ($array as $key => $element) {
            // *grumbles* stupid PHP type system
            $key = (string)$key;

            if ($field !== null && $field !== '') {
                // @phpstan-ignore-next-line
                if (\is_object($element) && \property_exists($element, $field) && $element->{$field} === $search) {
                    return $key;
                }

                if (\is_array($element) && $element[$field] === $search) {
                    return $key;
                }

                if (\is_scalar($element) && $element === $search) {
                    return $key;
                }
            } elseif (\is_object($element)) {
                $element = (array)$element;
                if (\in_array($search, $element, true)) {
                    return $key;
                }
            } elseif (\is_array($element) && \in_array($search, $element, true)) {
                return $key;
            } elseif (\is_scalar($element) && $element === $search) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Returns an array containing all the elements of arr1 after applying
     * the callback function to each one.
     *
     * @param array    $array      An array to run through the callback function
     * @param callable $callback   Callback function to run for each element in each array
     * @param bool     $onNoScalar Whether to call the callback function on non-scalar values
     *                             (Objects, resources, etc)
     */
    public static function mapDeep(array $array, callable $callback, bool $onNoScalar = false): array
    {
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $args        = [$value, $callback, $onNoScalar];
                $array[$key] = \call_user_func_array([__CLASS__, __FUNCTION__], $args);
            } elseif (\is_scalar($value) || $onNoScalar) {
                $array[$key] = $callback($value);
            }
        }

        return $array;
    }

    /**
     * Clean array by custom rule.
     */
    public static function clean(array $haystack): array
    {
        return \array_filter($haystack);
    }

    /**
     * Clean array before serialize to JSON.
     */
    public static function cleanBeforeJson(array $array): array
    {
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $array[$key] = self::cleanBeforeJson($value);
            }

            if ($array[$key] === '' || $array[$key] === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Check is array is type assoc.
     * @deprecated Use !\array_is_list() instead of Arr::isAssoc()
     */
    public static function isAssoc(array $array): bool
    {
        return !\array_is_list($array);
    }

    /**
     * Add cell to the start of assoc array.
     */
    public static function unshiftAssoc(array &$array, int|string $key, mixed $value): array
    {
        $array       = \array_reverse($array, true);
        $array[$key] = $value;
        $array       = \array_reverse($array, true);

        return $array;
    }

    /**
     * Get one field from array of arrays (array of objects).
     */
    public static function getField(array $arrayList, string $fieldName = 'id'): array
    {
        $result = [];

        foreach ($arrayList as $option) {
            if (\is_array($option)) {
                $result[] = $option[$fieldName];
            } elseif (\is_object($option)) {
                // @phpstan-ignore-next-line
                if (isset($option->{$fieldName})) {
                    // @phpstan-ignore-next-line
                    $result[] = $option->{$fieldName};
                }
            }
        }

        return $result;
    }

    /**
     * Group array by key and return list of grouped values.
     */
    public static function groupByKey(array $arrayList, string $key = 'id'): array
    {
        $result = [];

        foreach ($arrayList as $item) {
            if (\is_object($item)) {
                // @phpstan-ignore-next-line
                if (isset($item->{$key})) {
                    // @phpstan-ignore-next-line
                    $result[$item->{$key}][] = $item;
                }
            } elseif (\is_array($item)) {
                if (\array_key_exists($key, $item)) {
                    $result[$item[$key]][] = $item;
                }
            }
        }

        return $result;
    }

    /**
     * Recursive array mapping.
     */
    public static function map(\Closure $function, array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $result[$key] = self::map($function, $value);
            } else {
                $result[$key] = $function($value);
            }
        }

        return $result;
    }

    /**
     * Sort an array by keys based on another array.
     */
    public static function sortByArray(array $array, array $orderArray): array
    {
        return \array_merge(\array_flip($orderArray), $array);
    }

    /**
     * Add some prefix to each key.
     */
    public static function addEachKey(array $array, string $prefix): array
    {
        $result = [];

        foreach ($array as $key => $item) {
            $result[$prefix . $key] = $item;
        }

        return $result;
    }

    /**
     * Convert assoc array to comment style.
     */
    public static function toComment(array $data): string
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[] = $key . ': ' . $value . ';';
        }

        return \implode(\PHP_EOL, $result);
    }

    /**
     * Wraps its argument in an array unless it is already an array.
     *
     * @example
     *   Arr.wrap(null)      # => []
     *   Arr.wrap([1, 2, 3]) # => [1, 2, 3]
     *   Arr.wrap(0)         # => [0]
     */
    public static function wrap(mixed $object): array
    {
        if ($object === null) {
            return [];
        }

        if (\is_array($object) && \array_is_list($object)) {
            return $object;
        }

        return [$object];
    }

    /**
     * Array imploding for nested array.
     */
    public static function implode(string $glue, array $array): string
    {
        $result = '';

        foreach ($array as $item) {
            if (\is_array($item)) {
                $result .= self::implode($glue, $item) . $glue;
            } else {
                $result .= $item . $glue;
            }
        }

        if (!isStrEmpty($glue)) {
            $result = Str::sub($result, 0, 0 - Str::len($glue));
        }

        return $result;
    }

    /**
     * Remove all items from array by value.
     */
    public static function removeByValue(array $array, null|bool|float|int|string $value): array
    {
        return \array_filter(
            $array,
            static fn (null|bool|float|int|string $arrayItem): bool => $value !== $arrayItem,
            \ARRAY_FILTER_USE_BOTH,
        );
    }

    /**
     * Returns type of variables as array schema.
     */
    public static function getSchema(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $result[$key] = self::getSchema($value);
            } elseif (\is_object($value)) {
                $result[$key] = '\\' . \get_class($value);
            } else {
                $result[$key] = \get_debug_type($value);
            }
        }

        return $result;
    }
}
