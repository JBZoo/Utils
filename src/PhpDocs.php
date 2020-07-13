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
 * Class PhpDocs
 * @package JBZoo\Utils
 */
class PhpDocs
{
    /**
     * Simple parse of PHPDocs.
     * Example or return value
     *  [
     *      'description' => 'Simple parse of PHPDocs. Example or return value',
     *      'params'      => [
     *          'param'  => ['string $phpDoc'],
     *          'return' => ['array']
     *      ]
     *  ]
     *
     * @param string $phpDoc
     * @return array
     */
    public static function parse(string $phpDoc): array
    {
        $result = [
            'description' => '',
            'params'      => [],
        ];

        // split at each line
        $lines = (array)preg_split("/(\r?\n)/", $phpDoc);
        foreach ($lines as $line) {
            // if starts with an asterisk
            if (preg_match('/^(?=\s+?\*[^\/])(.+)/', (string)$line, $matches)) {
                // remove wrapping whitespace
                $info = trim($matches[1]);

                // remove leading asterisk
                $info = (string)preg_replace('/^(\*\s+?)/', '', $info);

                // if it doesn't start with an "@" symbol
                // then add to the description

                $firstChar = $info[0] ?? null;
                if ($firstChar !== "@") {
                    $result['description'] .= "\n$info";
                    continue;
                }

                // get the name of the param
                preg_match('/@(\w+)/', $info, $matches);
                $paramName = $matches[1];

                // remove the param from the string
                $value = str_replace("@{$paramName} ", '', $info);

                // if the param hasn't been added yet, create a key for it
                if (!isset($result['params'][$paramName])) {
                    $result['params'][$paramName] = [];
                }

                // push the param value into place
                $result['params'][$paramName][] = trim($value);
            }
        }

        $result['description'] = trim($result['description']);

        return $result;
    }
}
