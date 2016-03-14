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
     * @param $input
     * @param int $size
     * @param string $defaultImage
     */
    public function testGetGravatarUrl($input, $size, $defaultImage)
    {
        isLike(
            sprintf(
                '/http:\/\/www\.gravatar\.com\/avatar\/.*\?s=%d&d=%s/',
                $size,
                $defaultImage
            ),
            Email::getGravatarUrl($input[0], $input[1], $input[2])
        );

        $_SERVER['HTTPS'] = 'on';
        isLike(
            sprintf(
                '/https:\/\/secure\.gravatar\.com\/avatar\/.*\?s=%d&d=%s/',
                $size,
                $defaultImage
            ),
            Email::getGravatarUrl($input[0], $input[1], $input[2])
        );
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
            array(
                array(
                    'test@test.pt',
                    32,
                    null
                ),
                32,
                Email::getGravatarBuiltInDefaultImage()
            ),
            array(
                array(
                    'test@test.pt',
                    5000,
                    null
                ),
                2048,
                Email::getGravatarBuiltInDefaultImage()
            ),
            array(
                array(
                    'test@test.pt',
                    2047,
                    'monsterid'
                ),
                2047,
                'monsterid'
            ),
            array(
                array(
                    'test@test.pt',
                    -1,
                    null
                ),
                32,
                Email::getGravatarBuiltInDefaultImage()
            ),
            array(
                array(
                    'test@test.pt',
                    8.1,
                    'http://example.com/images/avatar.jpg'
                ),
                8,
                'http%3A%2F%2Fexample.com%2Fimages%2Favatar.jpg'
            ),
            array(
                array(
                    'test@test.pt',
                    15.5,
                    null
                ),
                15,
                Email::getGravatarBuiltInDefaultImage()
            ),
            array(
                array(
                    'test@test.pt',
                    "9000",
                    'IDEnticon'
                ),
                2048,
                'identicon'
            )
        );
    }
}
