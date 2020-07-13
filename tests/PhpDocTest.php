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

namespace JBZoo\PHPUnit;

use JBZoo\Utils\PhpDocs;
use JBZoo\Utils\Str;
use ReflectionClass;

/**
 * Class PhpDocTest
 *
 * @package JBZoo\PHPUnit
 */
class PhpDocTest extends PHPUnit
{
    public function testParse()
    {
        $oReflectionClass = new ReflectionClass(Str::class);
        $comment = $oReflectionClass->getMethod('splitSql')->getDocComment();

        isSame([
            'description' => "Splits a string of multiple queries into an array of individual queries.\n"
                . 'Single line or line end comments and multi line comments are stripped off.',
            'params'      => [

                'param'            => [
                    'string $sql Input SQL string with which to split into individual queries.'
                ],
                'return'           => [
                    "array"
                ],
                "SuppressWarnings" => [
                    "@SuppressWarnings(PHPMD.CyclomaticComplexity)",
                    "@SuppressWarnings(PHPMD.NPathComplexity)"
                ]
            ]
        ], PhpDocs::parse($comment));

        $oReflectionClass = new ReflectionClass(PhpDocs::class);
        $comment = $oReflectionClass->getMethod('parse')->getDocComment();

        isSame([
            'description' => implode("\n", [
                'Simple parse of PHPDocs.',
                'Example or return value',
                ' [',
                "     'description' => 'Simple parse of PHPDocs. Example or return value',",
                "     'params'      => [",
                "         'param'  => ['string \$phpDoc'],",
                "         'return' => ['array']",
                '     ]',
                ' ]',
            ]),
            'params'      => [
                'param'  => ['string $phpDoc'],
                'return' => ['array']
            ]
        ], PhpDocs::parse($comment));
    }
}