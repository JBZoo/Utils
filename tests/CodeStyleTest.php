<?php
/**
 * JBZoo Utils
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Utils
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Utils
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\PHPUnit;

/**
 * Class CodeStyleTest
 *
 * @package JBZoo\PHPUnit
 */
class CodeStyleTest extends Codestyle
{
    protected $_packageName   = 'Utils';
    protected $_packageAuthor = 'Denis Smetannikov <denis@jbzoo.com>';

    public function testCyrillic()
    {
        isTrue(true); // Don't check it
    }

    public function testHeadersCSS()
    {
        isTrue(true); // Don't check it
    }

    public function testHeadersJS()
    {
        isTrue(true); // Don't check it
    }

    public function testHeadersLESS()
    {
        isTrue(true); // Don't check it
    }

    public function testHeadersINI()
    {
        isTrue(true); // Don't check it
    }

    public function testHeadersSH()
    {
        isTrue(true); // Don't check it
    }

    public function testHeadersSQL()
    {
        isTrue(true); // Don't check it
    }

    public function testHeadersHtaccess()
    {
        isTrue(true); // Don't check it
    }
}
