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

use JBZoo\Utils\Slug;
use JBZoo\Utils\Str;

/**
 * Class PerformanceTest
 * @package JBZoo\PHPUnit
 */
class PerformanceTest extends PHPUnit
{
    public function getRandomString($length = 6)
    {
        $chars       = 'абвгд';
        $charsLength = strlen($chars);

        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[mt_rand(0, $charsLength - 1)];
        }

        return $result;
    }

    public function testSlugCache()
    {
        $_this = $this;

        runBench(array(
            // 2
            'Str::slug(2, false)' => function () use ($_this) {
                return Str::slug($_this->getRandomString(2), false);
            },
            'Str::slug(2, true)'  => function () use ($_this) {
                return Str::slug($_this->getRandomString(2), true);
            },
            'Slug::filter(2)'     => function () use ($_this) {
                return Slug::filter($_this->getRandomString(2));
            },

            // 3
            'Str::slug(3, false)' => function () use ($_this) {
                return Str::slug($_this->getRandomString(3), false);
            },
            'Str::slug(3, true)'  => function () use ($_this) {
                return Str::slug($_this->getRandomString(3), true);
            },
            'Slug::filter(3)'     => function () use ($_this) {
                return Slug::filter($_this->getRandomString(3));
            },

            // 4
            'Str::slug(4, false)'        => function () use ($_this) {
                return Str::slug($_this->getRandomString(4), false);
            },
            'Str::slug(4, true)'  => function () use ($_this) {
                return Str::slug($_this->getRandomString(4), true);
            },
            'Slug::filter(4)'     => function () use ($_this) {
                return Slug::filter($_this->getRandomString(4));
            },
        ), array('count' => 1000, 'name' => 'Random slug'));
    }

    public function testSlugSpeed()
    {
        $_this = $this;

        runBench(array(
            'Slug::filter'     => function () use ($_this) {
                return Slug::filter($_this->getRandomString(15));
            },
        ), array('count' => 1000, 'name' => 'Slug speed'));
    }
}
