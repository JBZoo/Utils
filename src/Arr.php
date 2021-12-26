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
 */

declare(strict_types=1);

namespace JBZoo\Utils;

use Closure;

/**
 * Class Arr
 *
 * @package JBZoo\Utils
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class Arr
{
    /**
     * Remove the duplicates from an array.
     *
     * @param array $array
     * @param bool  $keepKeys
     * @return array
     */
    public static function unique(array $array, bool $keepKeys = false): array
    {
        if ($keepKeys) {
            $array = \array_unique($array);
        } else {
            // This is faster version than the builtin array_unique().
            // http://stackoverflow.com/questions/8321620/array-unique-vs-array-flip
            // http://php.net/manual/en/function.array-unique.php
            $array = \array_keys(\array_flip($array));
        }

        return $array;
    }

    /**
     * Check if key exists
     *
     * @param string|int $key
     * @param array      $array
     * @param bool       $returnValue
     * @return mixed
     * @deprecated Use array_key_exists or ?: or ??
     */
    public static function key($key, array $array, bool $returnValue = false)
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
     * Check is value exists in the array
     *
     * @param mixed $value
     * @param array $array
     * @param bool  $returnKey
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function in($value, array $array, bool $returnKey = false)
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
     *
     * @param array $array
     * @return mixed
     */
    public static function first(array $array)
    {
        return \reset($array);
    }

    /**
     * Returns the last element in an array.
     *
     * @param array $array
     * @return mixed
     */
    public static function last(array $array)
    {
        return \end($array);
    }

    /**
     * Returns the first key in an array.
     *
     * @param array $array
     * @return int|string|null
     */
    public static function firstKey(array $array)
    {
        \reset($array);
        return \key($array);
    }

    /**
     * Returns the last key in an array.
     *
     * @param array $array
     * @return int|string|null
     */
    public static function lastKey(array $array)
    {
        \end($array);
        return \key($array);
    }

    /**
     * Flatten a multi-dimensional array into a one dimensional array.
     *
     * @param array $array        The array to flatten
     * @param bool  $preserveKeys Whether or not to preserve array keys. Keys from deeply nested arrays will
     *                            overwrite keys from shallow nested arrays
     * @return array
     */
    public static function flat(array $array, bool $preserveKeys = true): array
    {
        $flattened = [];

        \array_walk_recursive(
            $array,
            /**
             * @param mixed      $value
             * @param string|int $key
             */
            static function ($value, $key) use (&$flattened, $preserveKeys): void {
                if ($preserveKeys && !\is_int($key)) {
                    $flattened[$key] = $value;
                } else {
                    $flattened[] = $value;
                }
            }
        );

        return $flattened;
    }

    /**
     * Searches for a given value in an array of arrays, objects and scalar values. You can optionally specify
     * a field of the nested arrays and objects to search in.
     *
     * @param array       $array  The array to search
     * @param mixed       $search The value to search for
     * @param string|null $field  The field to search in, if not specified all fields will be searched
     * @return bool|mixed  False on failure or the array key on success
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function search(array $array, $search, ?string $field = null)
    {
        // *grumbles* stupid PHP type system
        $search = (string)$search;
        foreach ($array as $key => $element) {
            // *grumbles* stupid PHP type system

            $key = (string)$key;

            if ($field) {
                if (\is_object($element) && $element->{$field} === $search) {
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
                if (\in_array($search, $element, false)) {
                    return $key;
                }
            } elseif (\is_array($element) && \in_array($search, $element, false)) {
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
     * @param bool     $onNoScalar Whether or not to call the callback function on non scalar values
     *                             (Objects, resources, etc)
     * @return array
     */
    public static function mapDeep(array $array, callable $callback, bool $onNoScalar = false): array
    {
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $args = [$value, $callback, $onNoScalar];
                $array[$key] = \call_user_func_array([__CLASS__, __FUNCTION__], $args);
            } elseif (\is_scalar($value) || $onNoScalar) {
                $array[$key] = $callback($value);
            }
        }

        return $array;
    }

    /**
     * Clean array by custom rule
     *
     * @param array $haystack
     * @return array
     */
    public static function clean(array $haystack): array
    {
        return \array_filter($haystack);
    }

    /**
     * Clean array before serialize to JSON
     *
     * @param array $array
     * @return array
     */
    public static function cleanBeforeJson(array $array): array
    {
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $array[$key] = self::cleanBeforeJson($value);
            }

            if ($array[$key] === '' || null === $array[$key]) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Check is array is type assoc
     *
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        return \array_keys($array) !== \range(0, \count($array) - 1);
    }

    /**
     * Add cell to the start of assoc array
     *
     * @param array      $array
     * @param string|int $key
     * @param mixed      $value
     * @return array
     */
    public static function unshiftAssoc(array &$array, $key, $value): array
    {
        $array = \array_reverse($array, true);
        $array[$key] = $value;
        $array = \array_reverse($array, true);

        return $array;
    }

    /**
     * Get one field from array of arrays (array of objects)
     *
     * @param array  $arrayList
     * @param string $fieldName
     * @return array
     */
    public static function getField(array $arrayList, string $fieldName = 'id'): array
    {
        $result = [];

        foreach ($arrayList as $option) {
            if (\is_array($option)) {
                $result[] = $option[$fieldName];
            } elseif (\is_object($option)) {
                if (isset($option->{$fieldName})) {
                    $result[] = $option->{$fieldName};
                }
            }
        }

        return $result;
    }

    /**
     * Group array by key
     *
     * @param array  $arrayList
     * @param string $key
     * @return array
     */
    public static function groupByKey(array $arrayList, string $key = 'id'): array
    {
        $result = [];

        foreach ($arrayList as $item) {
            if (\is_object($item)) {
                if (isset($item->{$key})) {
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
     * Recursive array mapping
     *
     * @param Closure $function
     * @param array   $array
     * @return array
     */
    public static function map(Closure $function, array $array): array
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
     * Sort an array by keys based on another array
     *
     * @param array $array
     * @param array $orderArray
     * @return array
     */
    public static function sortByArray(array $array, array $orderArray): array
    {
        return \array_merge(\array_flip($orderArray), $array);
    }

    /**
     * Add some prefix to each key
     *
     * @param array  $array
     * @param string $prefix
     * @return array
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
     * Convert assoc array to comment style
     *
     * @param array $data
     * @return string
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
     * Wraps its argument in an array unless it is already an array
     *
     * @param mixed $object
     * @return array
     * @example
     *   Arr.wrap(null)      # => []
     *   Arr.wrap([1, 2, 3]) # => [1, 2, 3]
     *   Arr.wrap(0)         # => [0]
     *
     */
    public static function wrap($object): array
    {
        if (null === $object) {
            return [];
        }

        if (\is_array($object) && !self::isAssoc($object)) {
            return $object;
        }

        return [$object];
    }

    /**
     * Array imploding for nested array
     *
     * @param string $glue
     * @param array  $array
     * @return string
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

        if ($glue) {
            $result = Str::sub($result, 0, 0 - Str::len($glue));
        }

        return $result;
    }

    /**
     * @param array                      $array
     * @param string|float|int|bool|null $value
     * @return array
     */
    public static function removeByValue(array $array, $value): array
    {
        return \array_filter(
            $array,
            /**
             * @param string|float|int|bool|null $arrayItem
             */
            static function ($arrayItem) use ($value): bool {
                return $value !== $arrayItem;
            },
            \ARRAY_FILTER_USE_BOTH
        );
    }
}
