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

final class Xml
{
    public const VERSION  = '1.0';
    public const ENCODING = 'UTF-8';

    /**
     * Escape string before save it as xml content.
     */
    public static function escape(null|float|int|string $rawXmlContent): string
    {
        $rawXmlContent = (string)$rawXmlContent;

        $rawXmlContent = (string)\preg_replace(
            '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u',
            ' ',
            $rawXmlContent,
        );

        return \str_replace(
            ['&', '<', '>', '"', "'"],
            ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;'],
            $rawXmlContent,
        );
    }

    /**
     * Create DOMDocument object from XML-string.
     */
    public static function createFromString(?string $source = null, bool $preserveWhiteSpace = false): \DOMDocument
    {
        $document = new \DOMDocument();

        $document->preserveWhiteSpace = $preserveWhiteSpace;

        if (!isStrEmpty($source)) {
            $document->loadXML($source ?? '');
        }

        $document->xmlVersion   = self::VERSION;
        $document->encoding     = self::ENCODING;
        $document->formatOutput = true;

        return $document;
    }

    /**
     * Convert array to PHP DOMDocument object.
     * Format of input array
     * $source = [
     *     '_node'     => '#document',
     *     '_text'     => null,
     *     '_cdata'    => null,
     *     '_attrs'    => [],
     *     '_children' => [
     *         [
     *             '_node'     => 'parent',
     *             '_text'     => "Content of parent tag",
     *             '_cdata'    => null,
     *             '_attrs'    => ['parent-attribute' => 'value'],
     *             '_children' => [
     *                 [
     *                     '_node'     => 'child',
     *                     '_text'     => "Content of child tag",
     *                     '_cdata'    => null,
     *                     '_attrs'    => [],
     *                     '_children' => [],
     *                 ],
     *             ]
     *         ]
     *     ]
     * ];.
     *
     * Format of output
     *     <?xml version="1.0" encoding="UTF-8"?>
     *     <parent parent-attribute="value">Content of parent tag<child>Content of child tag</child></parent>
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @suppress PhanPossiblyFalseTypeArgumentInternal
     */
    public static function array2Dom(
        array $xmlAsArray,
        ?\DOMElement $domElement = null,
        ?\DOMDocument $document = null,
    ): \DOMDocument {
        if ($document === null) {
            $document = self::createFromString();
        }

        $domElement ??= $document;

        if (\array_key_exists('_text', $xmlAsArray) && $xmlAsArray['_text'] !== null) {
            $domElement->appendChild(new \DOMText($xmlAsArray['_text']));
        }

        if (\array_key_exists('_cdata', $xmlAsArray) && $xmlAsArray['_cdata'] !== null) {
            $domElement->appendChild($document->createCDATASection($xmlAsArray['_cdata']));
        }

        if ($domElement instanceof \DOMElement && \array_key_exists('_attrs', $xmlAsArray)) {
            foreach ($xmlAsArray['_attrs'] as $name => $value) {
                $domElement->setAttribute($name, $value);
            }
        }

        if (\array_key_exists('_children', $xmlAsArray)) {
            foreach ($xmlAsArray['_children'] as $mixedElement) {
                if (\array_key_exists('_node', $mixedElement) && $mixedElement['_node'][0] !== '#') {
                    $newNode = $document->createElement($mixedElement['_node']);
                    $domElement->appendChild($newNode);

                    /** @phan-suppress-next-line PhanPossiblyFalseTypeArgument */
                    self::array2Dom($mixedElement, $newNode, $document);
                }
            }
        }

        return $document;
    }

    /**
     * Convert PHP \DOMDocument or \DOMNode object to simple array
     * Format of input XML (as string)
     *     <?xml version="1.0" encoding="UTF-8"?>
     *     <parent parent-attribute="value">Content of parent tag<child>Content of child tag</child></parent>.
     *
     * Format of output array
     * $result = [
     *     '_node'     => '#document',
     *     '_text'     => null,
     *     '_cdata'    => null,
     *     '_attrs'    => [],
     *     '_children' => [
     *         [
     *             '_node'     => 'parent',
     *             '_text'     => "Content of parent tag",
     *             '_cdata'    => null,
     *             '_attrs'    => ['parent-attribute' => 'value'],
     *             '_children' => [
     *                 [
     *                     '_node'     => 'child',
     *                     '_text'     => "Content of child tag",
     *                     '_cdata'    => null,
     *                     '_attrs'    => [],
     *                     '_children' => [],
     *                 ],
     *             ]
     *         ]
     *     ]
     * ];
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function dom2Array(\DOMNode $element): array
    {
        $result = [
            '_node'     => $element->nodeName,
            '_text'     => null,
            '_cdata'    => null,
            '_attrs'    => [],
            '_children' => [],
        ];

        if ($element->attributes !== null && $element->hasAttributes()) {
            /** @var \DOMAttr $domAttr */
            foreach ($element->attributes as $domAttr) {
                $result['_attrs'][$domAttr->name] = $domAttr->value;
            }
        }

        if ($element->hasChildNodes()) {
            $children = $element->childNodes;

            $child = $children->item(0);
            if ($children->length === 1 && $child !== null) {
                if ($child->nodeType === \XML_TEXT_NODE) {
                    $result['_text'] = $child->nodeValue;

                    return $result;
                }

                if ($child->nodeType === \XML_CDATA_SECTION_NODE) {
                    $result['_cdata'] = $child->nodeValue;

                    return $result;
                }
            }

            foreach ($children as $child2) {
                if ($child2->nodeType !== \XML_COMMENT_NODE) {
                    $result['_children'][] = self::dom2Array($child2);
                }
            }
        }

        return $result;
    }
}
