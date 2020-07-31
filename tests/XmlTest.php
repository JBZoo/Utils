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

use JBZoo\Utils\Xml;

/**
 * Class XmlTest
 *
 * @package JBZoo\PHPUnit
 */
class XmlTest extends PHPUnit
{
    private $xmlFixture = PROJECT_ROOT . '/tests/resources/some-xml-file.xml';

    /**
     * @var array
     */
    private $expected = [
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
    private $expectedXml = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        '<phpunit bootstrap="tests/autoload.php" convertErrorsToExceptions="true" convertNoticesToExceptions="true" convertWarningsToExceptions="true" convertDeprecationsToExceptions="true" executionOrder="random" processIsolation="false" stopOnError="false" stopOnFailure="false" stopOnIncomplete="false" stopOnSkipped="false" stopOnRisky="false">',
        '  <testsuites>',
        '    <testsuite name="PHPUnit">',
        '      <directory suffix="Test.php">tests</directory>',
        '    </testsuite>',
        '  </testsuites>',
        '  <filter>',
        '    <whitelist processUncoveredFilesFromWhitelist="true">',
        '      <directory suffix=".php">src</directory>',
        '    </whitelist>',
        '  </filter>',
        '  <logging>',
        '    <log type="coverage-clover" target="build/coverage_xml/main.xml"/>',
        '    <log type="coverage-php" target="build/coverage_cov/main.cov"/>',
        '    <log type="junit" target="build/coverage_junit/main.xml"/>',
        '    <log type="coverage-text" target="php://stdout" showUncoveredFiles="false" showOnlySummary="true"/>',
        '  </logging>',
        '</phpunit>',
        '',
    ];

    /**
     * @var array
     */
    private $minimalSource = [
        '_children' => [
            [
                '_node'     => 'phpunit',
                '_attrs'    => [
                    'bootstrap'                       => 'tests/autoload.php',
                    'convertErrorsToExceptions'       => 'true',
                    'convertNoticesToExceptions'      => 'true',
                    'convertWarningsToExceptions'     => 'true',
                    'convertDeprecationsToExceptions' => 'true',
                    'executionOrder'                  => 'random',
                    'processIsolation'                => 'false',
                    'stopOnError'                     => 'false',
                    'stopOnFailure'                   => 'false',
                    'stopOnIncomplete'                => 'false',
                    'stopOnSkipped'                   => 'false',
                    'stopOnRisky'                     => 'false',
                ],
                '_children' => [
                    [
                        '_node'     => 'testsuites',
                        '_children' => [
                            [
                                '_node'     => 'testsuite',
                                '_attrs'    => ['name' => 'PHPUnit'],
                                '_children' => [
                                    [
                                        '_node'  => 'directory',
                                        '_text'  => 'tests',
                                        '_attrs' => ['suffix' => 'Test.php'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        '_node'     => 'filter',
                        '_children' => [
                            [
                                '_node'     => 'whitelist',
                                '_attrs'    => ['processUncoveredFilesFromWhitelist' => 'true'],
                                '_children' => [
                                    [
                                        '_node'  => 'directory',
                                        '_text'  => 'src',
                                        '_attrs' => ['suffix' => '.php'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        '_node'     => 'logging',
                        '_children' => [
                            [
                                '_node'  => 'log',
                                '_attrs' => ['type' => 'coverage-clover', 'target' => 'build/coverage_xml/main.xml'],
                            ],
                            [
                                '_node'  => 'log',
                                '_attrs' => ['type' => 'coverage-php', 'target' => 'build/coverage_cov/main.cov'],
                            ],
                            [
                                '_node'  => 'log',
                                '_attrs' => ['type' => 'junit', 'target' => 'build/coverage_junit/main.xml'],
                            ],
                            [
                                '_node'  => 'log',
                                '_attrs' => [
                                    'type'               => 'coverage-text',
                                    'target'             => 'php://stdout',
                                    'showUncoveredFiles' => 'false',
                                    'showOnlySummary'    => 'true',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    public function testEscape()
    {
        isSame(
            '&lt;a href=&quot;/test&quot;&gt;Test!@#$%^&amp;*()_+\/&lt;/a&gt;',
            Xml::escape('<a href="/test">Test!@#$%^&*()_+\\/</a>')
        );
    }

    public function testDomToArray()
    {
        $xmlString = file_get_contents($this->xmlFixture);
        $xmlAsArray = Xml::dom2Array(Xml::createFromString($xmlString));

        isSame($this->expected, $xmlAsArray);

        isSame(Xml::createFromString($xmlString)->saveXML(), Xml::array2Dom($xmlAsArray)->saveXML());
    }

    public function testArrayToDomToArray()
    {
        $xmlString = file_get_contents(PROJECT_ROOT . '/phpunit.xml.dist');
        $xmlAsArray = Xml::dom2Array(Xml::createFromString($xmlString));

        $xmlDocument = Xml::array2Dom($xmlAsArray);
        isClass(\DOMDocument::class, $xmlDocument);

        isSame(implode("\n", $this->expectedXml), $xmlDocument->saveXML());
    }

    public function testArrayToDomMinimal()
    {
        $actual = Xml::dom2Array(Xml::array2Dom($this->minimalSource));

        $xmlString = file_get_contents(PROJECT_ROOT . '/phpunit.xml.dist');
        $expected = Xml::dom2Array(Xml::createFromString($xmlString));

        isSame($expected, $actual);
    }

    public function testPhpDocs()
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
