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

namespace JBZoo\PHPUnit;

/**
 * Class CodeStyleTest
 * @package JBZoo\PHPUnit
 */
class CodestyleTest extends Codestyle
{
    protected $_packageName = 'Utils';
    protected $_packageAuthor = 'Denis Smetannikov <denis@jbzoo.com>';

    public function testCyrillic()
    {
        $this->_excludeFiles[] = 'Slug.php';
        $this->_excludeFiles[] = 'Vars.php';
        $this->_excludeFiles[] = 'SlugTest.php';
        $this->_excludeFiles[] = 'StringTest.php';
        $this->_excludeFiles[] = 'VarsTest.php';

        parent::testCyrillic();
    }

}
