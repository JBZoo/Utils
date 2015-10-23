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

/**
 * Class SlugTest
 * @package JBZoo\PHPUnit
 */
class SlugTest extends PHPUnit
{

    public function test()
    {
        isTrue(Slug::seemsUTF8('Денис'));

        is('A', Slug::removeAccents("\xC3\x81"));
        is('e', Slug::removeAccents("\xC4\x97"));
        is('U', Slug::removeAccents("\xC3\x9C"));
        is('Ae', Slug::removeAccents("Ä", 'de'));
        is('OEoeAEDHTHssaedhth', Slug::removeAccents(chr(140) . chr(156) . chr(198) . chr(208) . chr(222)
            . chr(223) . chr(230) . chr(240) . chr(254)));

        $input  = 'Benoit! à New-York? j’ai perçu 1 % : Qu’as-tu "gagné" chez M. V. Noël? Dix francs.';
        $expect = 'Benoit! a New-York? j’ai percu 1 % : Qu’as-tu "gagne" chez M. V. Noel? Dix francs.';
        is($expect, Slug::removeAccents($input));
    }

}
