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
 * Class Csv
 *
 * @package JBZoo\Utils
 */
class Csv
{
    public const LENGTH_LIMIT = 10000000;

    /**
     * Simple parser for CSV files
     *
     * @param string $csvFile
     * @param string $delimiter
     * @param string $enclosure
     * @param bool   $hasHeader
     * @return array
     */
    public static function parse(
        string $csvFile,
        string $delimiter = ';',
        string $enclosure = '"',
        bool $hasHeader = true
    ): array {
        $result = [];

        $headerKeys = [];
        $rowCounter = 0;

        if (($handle = fopen($csvFile, 'rb')) !== false) {
            while (($row = fgetcsv($handle, self::LENGTH_LIMIT, $delimiter, $enclosure)) !== false) {
                $row = (array)$row;
                if ($rowCounter === 0 && $hasHeader) {
                    $headerKeys = $row;
                } elseif ($hasHeader) {
                    $assocRow = [];

                    foreach ($headerKeys as $colIndex => $colName) {
                        $colName = (string)$colName;
                        $assocRow[$colName] = $row[$colIndex];
                    }

                    $result[] = $assocRow;
                } else {
                    $result[] = $row;
                }

                $rowCounter++;
            }

            fclose($handle);
        }

        return $result;
    }
}
