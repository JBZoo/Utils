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
 * Class Ob
 * @package JBZoo\Utils
 */
class Ob
{
    /**
     * Clean all ob_* buffers
     */
    public static function clean()
    {
        while (@ob_end_clean()) {
            // noop
        }
    }
}
