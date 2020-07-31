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

use JBZoo\Utils\Arr;
use JBZoo\Utils\Cli;
use JBZoo\Utils\Csv;
use JBZoo\Utils\Dates;
use JBZoo\Utils\Email;
use JBZoo\Utils\Env;
use JBZoo\Utils\Filter;
use JBZoo\Utils\FS;
use JBZoo\Utils\Http;
use JBZoo\Utils\Image;
use JBZoo\Utils\IP;
use JBZoo\Utils\PhpDocs;
use JBZoo\Utils\Ser;
use JBZoo\Utils\Slug;
use JBZoo\Utils\Stats;
use JBZoo\Utils\Str;
use JBZoo\Utils\Sys;
use JBZoo\Utils\Timer;
use JBZoo\Utils\Url;
use JBZoo\Utils\Vars;
use JBZoo\Utils\Xml;
use ReflectionClass;

/**
 * Class ReadmeTest
 *
 * @package JBZoo\PHPUnit
 */
class ReadmeTest extends PHPUnit
{
    public function testDocs()
    {
        if (Env::isExists('TRAVIS')) {
            skip("Disabled test. It's only for local using");
        }

        $classes = [
            Arr::class,
            Cli::class,
            Csv::class,
            Dates::class,
            Email::class,
            Env::class,
            Filter::class,
            FS::class,
            Http::class,
            Image::class,
            IP::class,
            Ser::class,
            Slug::class,
            Stats::class,
            Str::class,
            Sys::class,
            Timer::class,
            Url::class,
            Vars::class,
            PhpDocs::class,
            Xml::class,
        ];

        sort($classes, SORT_NATURAL);

        $readme = file_get_contents(PROJECT_ROOT . '/README.md');

        $expected = '';
        foreach ($classes as $class) {
            $expected .= $this->renderClass($class);
        }

        if (strpos($readme, $expected) === false) {
            is($expected, $readme, 'Just to see difference');
        }

        success();
    }

    /**
     * @param string $className
     * @return string
     */
    public function renderClass($className)
    {
        $methods = $this->parseClass($className);
        $realDocs = [
            "### {$className}",
            '',
            '```php',
        ];

        foreach ($methods as $methodName => $method) {
            if (!$method['comment']) {
                fail("Method {$className}::{$methodName } doesn't have comment");
            } elseif (strpos($method['comment'], "\n") === false) {
                $realDocs[] = "{$method['sign']} // {$method['comment']}";
            } else {
                $realDocs[] = '// ' . str_replace("\n", "\n// ", $method['comment']);
                $realDocs[] = $method['sign'];
            }

            $realDocs[] = '';
        }

        $realDocs[] = '```';
        $realDocs[] = '';
        $realDocs[] = '';
        $realDocs[] = '';

        return implode("\n", $realDocs);
    }

    /**
     * @param string $class
     * @return array
     */
    private function parseClass($class): array
    {
        $result = [];
        $className = Str::getClassName($class);

        $oReflectionClass = new ReflectionClass($class);
        foreach ($oReflectionClass->getMethods() as $method) {
            if (!$method->isPublic() || !$method->isStatic()) {
                continue;
            }

            $arguments = [];
            foreach ($method->getParameters() as $parameter) {
                $typeString = '';
                if ($parameter->hasType()) {
                    $type = $parameter->getType();
                    $typeString = $type->allowsNull() ? "?{$type} " : "{$type} ";
                }

                $defaultValue = '';
                if ($parameter->isOptional()) {
                    $defValue = $parameter->getDefaultValue();
                    if (is_string($defValue)) {
                        $defValue = "'{$defValue}'";
                    } elseif (null === $defValue) {
                        $defValue = 'null';
                    } elseif (is_array($defValue)) {
                        $defValue = '[]';
                    } elseif (false === $defValue) {
                        $defValue = 'false';
                    } elseif (true === $defValue) {
                        $defValue = 'true';
                    }
                    $defaultValue = " = {$defValue}";
                }

                $arguments[] = "{$typeString}\${$parameter->name}{$defaultValue}";
            }

            $arguments = implode(', ', $arguments);

            $returnTypePrint = '';
            if ($method->hasReturnType()) {
                $returnType = $method->getReturnType();
                $returnTypePrint = $returnType->allowsNull() ? ": ?{$returnType}" : ": {$returnType}";
            }

            $comment = PhpDocs::parse($method->getDocComment())['description'];
            $methodName = $method->getName();

            $result[$methodName] = [
                'sign'    => "{$className}::{$methodName}({$arguments}){$returnTypePrint};",
                'comment' => $comment,
            ];
        }

        ksort($result, SORT_NATURAL);

        return $result;
    }
}