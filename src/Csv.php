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
 * Class Csv
 * @package JBZoo\Utils
 */
class Csv
{
    const LENGTH_LIMIT = 10000000;

    /**
     * @param string $csvFile
     * @param string $delimiter
     * @param string $enclosure
     * @param bool   $hasHeader
     * @return array
     */
    public static function parse($csvFile, $delimiter = ';', $enclosure = '"', $hasHeader = true)
    {
        $result = array();

        $headerKeys = array();
        $rowCounter = 0;

        if (($handle = fopen($csvFile, "r")) !== false) {
            while (($row = fgetcsv($handle, self::LENGTH_LIMIT, $delimiter, $enclosure)) !== false) {
                if ($rowCounter === 0 && $hasHeader) {
                    $headerKeys = $row;

                } else {
                    if ($hasHeader) {
                        $assocRow = array();
                        foreach ($headerKeys as $colIndex => $colName) {
                            $assocRow[$colName] = $row[$colIndex];
                        }

                        $result[] = $assocRow;

                    } else {
                        $result[] = $row;
                    }
                }

                $rowCounter++;
            }

            fclose($handle);
        }

        return $result;
    }
}
