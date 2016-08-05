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
 * @author    Luís Nóbrega <luis.barros.nobrega@gmail.com>
 */

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Email;

/**
 * Class EmailTest
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
        is(array(), Email::check($input));
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
        is(array(), Email::getDomain($input));
    }

    public function testGetDomainsWithStringParam()
    {
        is(array('test.pt'), Email::getDomain('test@test.pt'));
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
        is(array('test.pt'), Email::getDomainSorted(array('test@test.pt')));
    }

    /**
     * @dataProvider getGravatarUrlProvider
     * @param        $input
     * @param string $expectedHttp
     * @param string $expectedHttps
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
        return array(
            array(
                array(
                    'test@gmail.com',
                    'test@hotmail.com',
                ),
                array(
                    'test@gmail.com',
                    'test@hotmail.com',
                ),
            ),
            array(
                array(
                    'test@gmail@.com',
                    'test@gmailcom',
                    'test@',
                    'test@hotmail.com',
                    'test',
                    '@test@gmail.com',
                ),
                array(
                    'test@hotmail.com',
                ),
            ),
        );
    }

    public function getDomainsProvider()
    {
        return array(
            array(
                array(
                    'test@test.pt',
                    'test@test.pt',
                ),
                array(
                    'test.pt',
                ),
            ),
            array(
                array(
                    'test@gmail.com',
                    'test@hotmail.com',
                ),
                array(
                    'gmail.com',
                    'hotmail.com',
                ),
            ),
            array(
                array(
                    'test@gmail@.com',
                    'test@gmailcom',
                    'test@',
                    'test@hotmail.com',
                    'test',
                    '@test@gmail.com',
                ),
                array(
                    'hotmail.com',
                ),
            ),
        );
    }

    public function getDomainsSortedProvider()
    {
        return array(
            array(
                array(
                    'test@abc.pt',
                    'test@cbc.pt',
                ),
                array(
                    'abc.pt',
                    'cbc.pt',
                ),
            ),
            array(
                array(
                    'test@gmail.com',
                    'test@hotmail.com',
                ),
                array(
                    'gmail.com',
                    'hotmail.com',
                ),
            ),
            array(
                array(
                    'test@zzzzzzcom',
                    'test@aaacom',
                ),
                array(),
            ),
        );
    }

    public function getEmptyProvider()
    {
        return array(
            array(
                array(),
            ),
            array(
                false,
            ),
            array(
                "",
            ),
            array(
                0,
            ),
        );
    }

    public function getGravatarUrlProvider()
    {
        return array(
            0 => array(
                array('test@test.pt', 32, null),
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon'
            ),
            1 => array(
                array('test@test.pt', 5000, null),
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2048&d=identicon',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2048&d=identicon',
            ),
            2 => array(
                array('test@test.pt', 2047, 'monsterid'),
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2047&d=monsterid',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2047&d=monsterid',
            ),
            3 => array(
                array('test@test.pt', -1, null),
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
            ),
            4 => array(
                array('test@test.pt', 8.1, 'https://example.com/images/avatar.jpg'),
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=https%3A%2F%2Fexample.com%2Fimages%2Favatar.jpg',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=https%3A%2F%2Fexample.com%2Fimages%2Favatar.jpg',
            ),
            5 => array(
                array('test@test.pt', 15.5, null),
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=32&d=identicon',
            ),
            6 => array(
                array('test@test.pt', " 9000 ", 'IDEnticon'),
                'http://www.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2048&d=identicon',
                'https://secure.gravatar.com/avatar/7c2cf316efa3b541b3ac76a950aea671/?s=2048&d=identicon',
            ),
            7 => array(
                array('admin@jbzoo.com', "9000", 'IDEnticon'),
                'http://www.gravatar.com/avatar/f27f28ab2158cd2cccc78c364d6247fe/?s=2048&d=identicon',
                'https://secure.gravatar.com/avatar/f27f28ab2158cd2cccc78c364d6247fe/?s=2048&d=identicon',
            )
        );
    }
}
