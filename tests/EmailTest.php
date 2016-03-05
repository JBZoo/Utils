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
     * @dataProvider getCheckDnsProvider
     * @param $input
     * @param $outcome
     */
    public function testCheckDns($input, $outcome)
    {
        is($outcome, Email::checkDns($input));
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
     * @dataProvider getDomainsInAlphabeticalOrderProvider
     * @param $input
     * @param $outcome
     */
    public function testGetDomainsInAlphabeticalOrder($input, $outcome)
    {
        is($outcome, Email::getDomainInAlphabeticalOrder($input));
    }

    public function testGetDomainsInAlphabeticalOrderWithOneSizeArray()
    {
        is(array('test.pt'), Email::getDomainInAlphabeticalOrder(array('test@test.pt')));
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

    public function getCheckDnsProvider()
    {
        return array(
            array(
                'test@gmail.com',
                true,
            ),
            array(
                'test@zzzzzzzzzzzzzzzzzzzzzz',
                false,
            ),
            array(
                '@test@',
                false,
            ),
            array(
                'fake.com@fake.commmmmmmmmm',
                false,
            ),
            array(
                '',
                false,
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

    public function getDomainsInAlphabeticalOrderProvider()
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
}
