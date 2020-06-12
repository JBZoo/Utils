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

use function JBZoo\Utils\alias;
use function JBZoo\Utils\alpha;
use function JBZoo\Utils\alphanum;
use function JBZoo\Utils\bool;
use function JBZoo\Utils\cleanCmd;
use function JBZoo\Utils\digits;
use function JBZoo\Utils\float;
use function JBZoo\Utils\int;
use function JBZoo\Utils\isPath;
use function JBZoo\Utils\slug;
use function JBZoo\Utils\strip;
use function JBZoo\Utils\stripQuotes;

/**
 * Class AliasesTest
 *
 * @package JBZoo\PHPUnit
 */
class AliasesTest extends PHPUnit
{
    public function testAliases()
    {
        isSame(1, int('1'));
        isSame(1.0, float('1'));
        isSame(true, bool('1'));

        isSame('qwer-ty', alias('Qwer ty'));
        isSame('qwer-ty', slug('Qwer ty'));
        isSame('12', digits('Qwer 1 ty2'));
        isSame('Qwerty', alpha('Qwer 1 ty2'));
        isSame('Qwer1ty2', alphanum(' #$% Qwer 1 ty2'));

        isSame('', isPath(__DIR__ . ':'));
        isSame(__DIR__, isPath(__DIR__));
        isSame('qwesaad', cleanCmd('qwe saad'));
        isSame("\$source = \'127.0001  some-WORD \' ;", strip("\$source = \'127.0001 <img> some-<b>WORD</b> \' ; "));
        isSame('adsa', stripQuotes('"adsa"'));
        isSame('"adsa', stripQuotes('"adsa'));
    }
}