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

use JBZoo\Utils\Email;

/**
 * Class EmailTest
 *
 * @package JBZoo\PHPUnit
 */
class EmailTest extends PHPUnit
{
    /**
     * @dataProvider getCheckProvider
     * @param $input
     * @param $outcome
     */
    public function testCheck($input, $outcome)
    {
        is($outcome, Email::check($input));
    }

    /**
     * @dataProvider getEmptyProvider
     * @param $input
     */
    public function testCheckWithEmptyEmails($input)
    {
        is([], Email::check($input));
    }

    /**
     * @dataProvider getDomainsProvider
     * @param $input
     * @param $outcome
     */
    public function testGetDomains($input, $outcome)
    {
        is($outcome, Email::getDomain($input));
    }

    /**
     * @dataProvider getEmptyProvider
     * @param $input
     */
    public function testGetDomainsWithEmptyEmails($input)
    {
        is([], Email::getDomain($input));
    }

    public function testGetDomainsWithStringParam()
    {
        is(['test.pt'], Email::getDomain('test@test.pt'));
    }

    /**
     * @dataProvider getDomainsSortedProvider
     * @param $input
     * @param $outcome
     */
    public function testGetDomainsInAlphabeticalOrder($input, $outcome)
    {
        is($outcome, Email::getDomainSorted($input));
    }

    public function testGetDomainsInAlphabeticalOrderWithOneSizeArray()
    {
        is(['test.pt'], Email::getDomainSorted(['test@test.pt']));
    }

    /**
     * @dataProvider getGravatarUrlProvider
     * @param        $input
     * @param string $expectedHttp
     * @param string $expectedHttps
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testGetGravatarUrl($input, $expectedHttp, $expectedHttps)
    {
        $_SERVER['HTTPS'] = 'off';
        isSame($expectedHttp, Email::getGravatarUrl($input[0], $input[1], $input[2]));

        $_SERVER['HTTPS'] = 'on';
        isSame($expectedHttps, Email::getGravatarUrl($input[0], $input[1], $input[2]));
    }

    /**
     * @dataProvider getEmptyProvider
     * @param $input
     */
    public function testGetGravatarUrlWithEmptyEmails($input)
    {
        is(null, Email::getGravatarUrl($input));
    }

    public function getCheckProvider()
    {
        return [
            [
                [
                    'test@gmail.com',
                    'test@hotmail.com',
                ],
                [
                    'test@gmail.com',
                    'test@hotmail.com',
                ],
            ],
            [
                [
                    'test@gmail@.com',
                    'test@gmailcom',
                    'test@',
                    'test@hotmail.com',
                    'test',
                    '@test@gmail.com',
                ],
                [
                    'test@hotmail.com',
                ],
            ],
        ];
    }

    public function getDomainsProvider()
    {
        return [
            [
                [
                    'test@test.pt',
                    'test@test.pt',
                ],
                [
                    'test.pt',
                ],
            ],
            [
                [
                    'test@gmail.com',
                    'test@hotmail.com',
                ],
                [
                    'gmail.com',
                    'hotmail.com',
                ],
            ],
            [
                [
                    'test@gmail@.com',
                    'test@gmailcom',
                    'test@',
                    'test@hotmail.com',
                    'test',
                    '@test@gmail.com',
                ],
                [
                    'hotmail.com',
                ],
            ],
        ];
    }

    public function getDomainsSortedProvider()
    {
        return [
            [
                [
                    'test@abc.pt',
                    'test@cbc.pt',
                ],
                [
                    'abc.pt',
                    'cbc.pt',
                ],
            ],
            [
                [
                    'test@gmail.com',
                    'test@hotmail.com',
                ],
                [
                    'gmail.com',
                    'hotmail.com',
                ],
            ],
            [
                [
                    'test@zzzzzzcom',
                    'test@aaacom',
                ],
                [],
            ],
        ];
    }

    public function getEmptyProvider()
    {
        return [[[]], [false], [''], [0],];
    }

    public function getGravatarUrlProvider()
    {
        return [
            0 => [
                ['test@test.pt', 32, null],
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
            ],
            1 => [
                ['test@test.pt', 5000, null],
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2048&d=identicon',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2048&d=identicon',
            ],
            2 => [
                ['test@test.pt', 2047, 'monsterid'],
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2047&d=monsterid',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2047&d=monsterid',
            ],
            3 => [
                ['test@test.pt', -1, null],
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
            ],
            4 => [
                ['test@test.pt', 8.1, 'https://example.com/images/avatar.jpg'],
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?'
                . 's=32&d=https%3A%2F%2Fexample.com%2Fimages%2Favatar.jpg',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?'
                . 's=32&d=https%3A%2F%2Fexample.com%2Fimages%2Favatar.jpg',
            ],
            5 => [
                ['test@test.pt', 15.5, null],
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
            ],
            6 => [
                ['test@test.pt', ' 9000 ', 'IDEnticon'],
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2048&d=identicon',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2048&d=identicon',
            ],
            7 => [
                ['admin@jbzoo.com', '9000', 'IDEnticon'],
                'http://www.gravatar.com/avatar/f27f28ab2158cd2cccc78c364d6247fe/?s=2048&d=identicon',
                'https://secure.gravatar.com/avatar/f27f28ab2158cd2cccc78c364d6247fe/?s=2048&d=identicon',
            ],
        ];
    }
}
