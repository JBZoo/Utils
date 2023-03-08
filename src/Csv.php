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

final class Csv
{
    public const LENGTH_LIMIT = 10000000;

    /**
     * Simple parser for CSV files.
     */
    public static function parse(
        string $csvFile,
        string $delimiter = ';',
        string $enclosure = '"',
        bool $hasHeader = true,
    ): array {
        $result = [];

        $headerKeys = [];
        $rowCounter = 0;

        if (($handle = \fopen($csvFile, 'r')) !== false) {
            while (($row = \fgetcsv($handle, self::LENGTH_LIMIT, $delimiter, $enclosure)) !== false) {
                if ($rowCounter === 0 && $hasHeader) {
                    $headerKeys = $row;
                } elseif ($hasHeader) {
                    $assocRow = [];

                    foreach ($headerKeys as $colIndex => $colName) {
                        $colName            = (string)$colName;
                        $assocRow[$colName] = $row[$colIndex];
                    }

                    $result[] = $assocRow;
                } else {
                    $result[] = $row;
                }

                $rowCounter++;
            }

            \fclose($handle);
        }

        return $result;
    }
}
