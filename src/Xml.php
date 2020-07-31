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
 * Class Xml
 * @package JBZoo\Utils
 */
class Xml
{
    public const VERSION  = '1.0';
    public const ENCODING = 'UTF-8';

    /**
     * Escape string before save it as xml content
     *
     * @param string $string
     * @return string
     */
    public static function escape(string $string): string
    {
        $string = (string)preg_replace(
            '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u',
            ' ',
            $string
        );

        $string = str_replace(
            ['&', '<', '>', '"', "'"],
            ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;'],
            $string
        );

        return $string;
    }

    /**
     * Create DOMDocument object from XML-string
     *
     * @param string|null $source
     * @return \DOMDocument
     */
    public static function createFromString(?string $source = null): \DOMDocument
    {
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;

        if ($source) {
            $document->loadXML($source);
        }

        $document->version = self::VERSION;
        $document->encoding = self::ENCODING;
        $document->formatOutput = true;

        return $document;
    }

    /**
     * Convert array to PHP DOMDocument object
     *
     * @param array             $xmlAsArray
     * @param \DOMElement|null  $domElement
     * @param \DOMDocument|null $document
     * @return \DOMDocument
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function array2Dom(
        array $xmlAsArray,
        ?\DOMElement $domElement = null,
        ?\DOMDocument $document = null
    ): \DOMDocument {
        if (null === $document) {
            $document = self::createFromString();
        }

        $domElement = $domElement ?? $document;

        if (array_key_exists('_text', $xmlAsArray) && $xmlAsArray['_text'] !== null) {
            $domElement->appendChild($document->createTextNode($xmlAsArray['_text']));
        }

        if (array_key_exists('_cdata', $xmlAsArray) && $xmlAsArray['_cdata'] !== null) {
            $domElement->appendChild($document->createCDATASection($xmlAsArray['_cdata']));
        }

        if ($domElement instanceof \DOMElement && array_key_exists('_attrs', $xmlAsArray)) {
            foreach ($xmlAsArray['_attrs'] as $name => $value) {
                $domElement->setAttribute($name, $value);
            }
        }

        if (array_key_exists('_children', $xmlAsArray)) {
            foreach ($xmlAsArray['_children'] as $mixedElement) {
                if (
                    array_key_exists('_node', $mixedElement) &&
                    '#comment' !== $mixedElement['_node'] &&
                    '#document' !== $mixedElement['_node']
                ) {
                    $node = $document->createElement($mixedElement['_node']);
                    $domElement->appendChild($node);

                    /** @phan-suppress-next-line PhanPossiblyFalseTypeArgument */
                    self::array2Dom($mixedElement, $node, $document);
                }
            }
        }

        return $document;
    }

    /**
     * Convert PHP \DOMDocument or \DOMNode object to simple array
     *
     * @param \DOMNode|\DOMElement|\DOMDocument $element
     * @return array
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

        if ($element->attributes && $element->hasAttributes()) {
            foreach ($element->attributes as $attr) {
                $result['_attrs'][$attr->name] = $attr->value;
            }
        }

        if ($element->hasChildNodes()) {
            $children = $element->childNodes;

            if ($children->length === 1 && $child = $children->item(0)) {
                if ($child->nodeType === XML_TEXT_NODE) {
                    $result['_text'] = $child->nodeValue;
                    return $result;
                }

                if ($child->nodeType === XML_CDATA_SECTION_NODE) {
                    $result['_cdata'] = $child->nodeValue;
                    return $result;
                }
            }

            foreach ($children as $child) {
                if ($child->nodeType !== XML_COMMENT_NODE) {
                    $result['_children'][] = self::dom2Array($child);
                }
            }
        }

        return $result;
    }
}
