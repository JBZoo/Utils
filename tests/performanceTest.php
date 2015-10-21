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
 */

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Package;

/**
 * Class PerformanceTest
 * @package JBZoo\Utils
 */
class PerformanceTest extends PHPUnit
{
    protected $_max = 1000;

    public function testLeakMemoryCreate()
    {
        if ($this->isXDebug()) {
            return;
        }

        $this->startProfiler();
        for ($i = 0; $i < $this->_max; $i++) {
            // Your code
            $obj = new Package();
            is('street magic', $obj->doSomeStreetMagic());
            unset($obj);
            // Your code
        }

        alert($this->loopProfiler($this->_max), 'Create - min');
    }
}
