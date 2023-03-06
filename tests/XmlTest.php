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

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Xml;

/**
 * Class XmlTest
 *
 * @package JBZoo\PHPUnit
 */
class XmlTest extends PHPUnit
{
    private string $xmlFixture = PROJECT_ROOT . '/tests/resources/some-xml-file.xml';

    /**
     * @var array
     */
    private array $expected = [
        '_node'     => '#document',
        '_text'     => null,
        '_cdata'    => null,
        '_attrs'    => [],
        '_children' => [
            [
                '_node'     => 'slideshow',
                '_text'     => null,
                '_cdata'    => null,
                '_attrs'    => [
                    'title'  => 'Sample Slide Show',
                    'date'   => 'Date of publication',
                    'author' => 'Yours Truly',
                ],
                '_children' => [
                    [
                        '_node'     => 'slide',
                        '_text'     => null,
                        '_cdata'    => null,
                        '_attrs'    => ['type' => 'all',],
                        '_children' => [
                            [
                                '_node'     => 'title',
                                '_text'     => 'Wake up to WonderWidgets!',
                                '_cdata'    => null,
                                '_attrs'    => [],
                                '_children' => [],
                            ],
                        ],
                    ],
                    [
                        '_node'     => 'slide',
                        '_text'     => null,
                        '_cdata'    => null,
                        '_attrs'    => ['type' => 'all',],
                        '_children' => [
                            [
                                '_node'     => 'title',
                                '_text'     => 'Overview',
                                '_cdata'    => null,
                                '_attrs'    => [],
                                '_children' => [],
                            ],
                            [
                                '_node'     => 'item',
                                '_text'     => null,
                                '_cdata'    => null,
                                '_attrs'    => [],
                                '_children' => [
                                    [
                                        '_node'     => 'em',
                                        '_text'     => 'WonderWidgets',
                                        '_cdata'    => null,
                                        '_attrs'    => [],
                                        '_children' => [],
                                    ],
                                ],
                            ],
                            [
                                '_node'     => 'item',
                                '_text'     => null,
                                '_cdata'    => null,
                                '_attrs'    => [],
                                '_children' => [],
                            ],
                            [
                                '_node'     => 'item',
                                '_text'     => null,
                                '_cdata'    => null,
                                '_attrs'    => [],
                                '_children' => [
                                    [
                                        '_node'     => 'em',
                                        '_text'     => 'buys',
                                        '_cdata'    => null,
                                        '_attrs'    => [],
                                        '_children' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * @var string[]
     */
    private array $expectedXml = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        '<phpunit bootstrap="tests/autoload.php" convertErrorsToExceptions="true" convertNoticesToExceptions="true" convertWarningsToExceptions="true" convertDeprecationsToExceptions="true" executionOrder="random" processIsolation="false" stopOnError="false" stopOnFailure="false" stopOnIncomplete="false" stopOnSkipped="false" stopOnRisky="false" noNamespaceSchemaLocation="https://schema.phpunit.de/9.3/phpunit.xsd">',
        '  <coverage processUncoveredFiles="true">',
        '    <include>',
        '      <directory suffix=".php">src</directory>',
        '    </include>',
        '    <report>',
        '      <clover outputFile="build/coverage_xml/main.xml"/>',
        '      <php outputFile="build/coverage_cov/main.cov"/>',
        '      <text outputFile="php://stdout" showUncoveredFiles="false" showOnlySummary="true"/>',
        '    </report>',
        '  </coverage>',
        '  <testsuites>',
        '    <testsuite name="PHPUnit">',
        '      <directory suffix="Test.php">tests</directory>',
        '    </testsuite>',
        '  </testsuites>',
        '  <logging>',
        '    <junit outputFile="build/coverage_junit/main.xml"/>',
        '  </logging>',
        '</phpunit>',
        '',
    ];

    /**
     * @var array
     */
    private array $minimalSource = [
        '_children' => [
            [
                "_node"     => "phpunit",
                "_text"     => null,
                "_cdata"    => null,
                "_attrs"    => [
                    "bootstrap"                       => "tests/autoload.php",
                    "convertErrorsToExceptions"       => "true",
                    "convertNoticesToExceptions"      => "true",
                    "convertWarningsToExceptions"     => "true",
                    "convertDeprecationsToExceptions" => "true",
                    "executionOrder"                  => "random",
                    "processIsolation"                => "false",
                    "stopOnError"                     => "false",
                    "stopOnFailure"                   => "false",
                    "stopOnIncomplete"                => "false",
                    "stopOnSkipped"                   => "false",
                    "stopOnRisky"                     => "false",
                    "noNamespaceSchemaLocation"       => "https://schema.phpunit.de/9.3/phpunit.xsd"
                ],
                "_children" => [
                    [
                        "_node"     => "coverage",
                        "_text"     => null,
                        "_cdata"    => null,
                        "_attrs"    => ["processUncoveredFiles" => "true"],
                        "_children" => [
                            [
                                "_node"     => "include",
                                "_text"     => null,
                                "_cdata"    => null,
                                "_attrs"    => [],
                                "_children" => [
                                    [
                                        "_node"     => "directory",
                                        "_text"     => "src",
                                        "_cdata"    => null,
                                        "_attrs"    => ["suffix" => ".php"],
                                        "_children" => []
                                    ]
                                ]
                            ],
                            [
                                "_node"     => "report",
                                "_text"     => null,
                                "_cdata"    => null,
                                "_attrs"    => [],
                                "_children" => [
                                    [
                                        "_node"     => "clover",
                                        "_text"     => null,
                                        "_cdata"    => null,
                                        "_attrs"    => ["outputFile" => "build/coverage_xml/main.xml"],
                                        "_children" => []
                                    ],
                                    [
                                        "_node"     => "php",
                                        "_text"     => null,
                                        "_cdata"    => null,
                                        "_attrs"    => ["outputFile" => "build/coverage_cov/main.cov"],
                                        "_children" => []
                                    ],
                                    [
                                        "_node"     => "text",
                                        "_text"     => null,
                                        "_cdata"    => null,
                                        "_attrs"    => [
                                            "outputFile"         => "php://stdout",
                                            "showUncoveredFiles" => "false",
                                            "showOnlySummary"    => "true"
                                        ],
                                        "_children" => []
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "_node"     => "testsuites",
                        "_text"     => null,
                        "_cdata"    => null,
                        "_attrs"    => [],
                        "_children" => [
                            [
                                "_node"     => "testsuite",
                                "_text"     => null,
                                "_cdata"    => null,
                                "_attrs"    => ["name" => "PHPUnit"],
                                "_children" => [
                                    [
                                        "_node"     => "directory",
                                        "_text"     => "tests",
                                        "_cdata"    => null,
                                        "_attrs"    => ["suffix" => "Test.php"],
                                        "_children" => []
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "_node"     => "logging",
                        "_text"     => null,
                        "_cdata"    => null,
                        "_attrs"    => [],
                        "_children" => [
                            [
                                "_node"     => "junit",
                                "_text"     => null,
                                "_cdata"    => null,
                                "_attrs"    => ["outputFile" => "build/coverage_junit/main.xml"],
                                "_children" => []
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    public function testEscape(): void
    {
        isSame(
            '&lt;a href=&quot;/test&quot;&gt;Test!@#$%^&amp;*()_+\/&lt;/a&gt;',
            Xml::escape('<a href="/test">Test!@#$%^&*()_+\\/</a>')
        );
    }

    public function testDomToArray(): void
    {
        $xmlString = file_get_contents($this->xmlFixture);
        $xmlAsArray = Xml::dom2Array(Xml::createFromString($xmlString));

        isSame($this->expected, $xmlAsArray);

        isSame(Xml::createFromString($xmlString)->saveXML(), Xml::array2Dom($xmlAsArray)->saveXML());
    }

    public function testArrayToDomToArray(): void
    {
        $xmlString = file_get_contents(PROJECT_ROOT . '/phpunit.xml.dist');
        $xmlAsArray = Xml::dom2Array(Xml::createFromString($xmlString));

        $xmlDocument = Xml::array2Dom($xmlAsArray);
        isClass(\DOMDocument::class, $xmlDocument);

        isSame(implode("\n", $this->expectedXml), $xmlDocument->saveXML());
    }

    public function testArrayToDomMinimal(): void
    {
        $actual = Xml::dom2Array(Xml::array2Dom($this->minimalSource));

        $xmlString = file_get_contents(PROJECT_ROOT . '/phpunit.xml.dist');
        $expected = Xml::dom2Array(Xml::createFromString($xmlString));

        isSame($expected, $actual);
    }

    public function testPhpDocs(): void
    {
        $source = [
            '_node'     => '#document',
            '_text'     => null,
            '_cdata'    => null,
            '_attrs'    => [],
            '_children' => [
                [
                    '_node'     => 'parent',
                    '_text'     => "Content of parent tag",
                    '_cdata'    => null,
                    '_attrs'    => ['parent-attribute' => 'value'],
                    '_children' => [
                        [
                            '_node'     => 'child',
                            '_text'     => "Content of child tag",
                            '_cdata'    => null,
                            '_attrs'    => [],
                            '_children' => [],
                        ],
                    ]
                ]
            ]
        ];

        $xmlDocument = Xml::array2Dom($source);
        isSame(implode("\n", [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<parent parent-attribute="value">Content of parent tag<child>Content of child tag</child></parent>',
            ''
        ]), $xmlDocument->saveXML());
    }
}
