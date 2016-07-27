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

use JBZoo\Utils\Url;

/**
 * Class UrlTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class UrlTest extends PHPUnit
{

    public function testRootPath()
    {
        $_SERVER['HTTP_HOST']    = 'test.dev';
        $_SERVER['SERVER_PORT']  = 80;
        $_SERVER['REQUEST_URI']  = '/test.php?foo=bar';
        $_SERVER['QUERY_STRING'] = 'foo=bar';
        $_SERVER['PHP_SELF']     = '/test.php';

        // Test regular.
        is('http://test.dev', Url::root());
        is('/test.php?foo=bar', Url::path());
        is('http://test.dev/test.php?foo=bar', Url::current());

        // Test server auth.
        $_SERVER['PHP_AUTH_USER'] = 'admin';
        $_SERVER['PHP_AUTH_PW']   = '123456';
        is('http://admin:123456@test.dev', Url::root(true));
        is('http://test.dev', Url::root(false));
        is('http://test.dev', Url::root());
        is('/test.php?foo=bar', Url::path());
        is('http://admin:123456@test.dev/test.php?foo=bar', Url::current(true));
        is('http://test.dev/test.php?foo=bar', Url::current(false));
        is('http://test.dev/test.php?foo=bar', Url::current());

        // Test port.
        unset($_SERVER['PHP_AUTH_USER']);
        unset($_SERVER['PHP_AUTH_PW']);
        $_SERVER['SERVER_PORT'] = 8080;
        is('http://test.dev:8080', Url::root());
        is('/test.php?foo=bar', Url::path());
        is('http://test.dev:8080/test.php?foo=bar', Url::current());

        // Test SSL.
        $_SERVER['HTTPS']       = 'on';
        $_SERVER['SERVER_PORT'] = 443;
        is('https://test.dev', Url::root());
        is('/test.php?foo=bar', Url::path());
        is('https://test.dev/test.php?foo=bar', Url::current());

        $_SERVER['SERVER_PORT'] = 8888;
        is('https://test.dev:8888', Url::root());
        is('/test.php?foo=bar', Url::path());
        is('https://test.dev:8888/test.php?foo=bar', Url::current());
        unset($_SERVER['HTTPS']);

        // Test no $_SERVER['REQUEST_URI'] (e.g., MS IIS).
        unset($_SERVER['REQUEST_URI']);
        $_SERVER['SERVER_PORT'] = 80;
        is('http://test.dev', Url::root());

        // Only for MS IIS
        //is('/test.php?foo=bar', Url::path());
        //is('http://test.dev/test.php?foo=bar', Url::current());
    }

    public function testParseLinky()
    {
        $input  = 'great websites: http://www.google.com?param=test and http://yahoo.com/a/nested/folder';
        $expect = 'great websites: <a href="http://www.google.com?param=test">http://www.google.com?param=test</a>'
            . ' and <a href="http://yahoo.com/a/nested/folder">http://yahoo.com/a/nested/folder</a>';

        is($expect, Url::parseLink($input));
        is($expect, Url::parseLink($expect));
    }

    public function testAddArg()
    {
        // Regular tests
        is('user=5', Url::addArg(array('user' => 5), ''));
        is('/app/admin/users?user=5', Url::addArg(array('user' => 5), '/app/admin/users'));
        is('/app/admin/users?action=edit&user=5', Url::addArg(array('user' => 5), '/app/admin/users?action=edit'));
        is('/app/admin/users?action=edit&tab=personal&user=5', Url::addArg(array('user' => 5), '/app/admin/users?action=edit&tab=personal'));

        // Ensure strips false.
        is('/index.php', Url::addArg(array('debug' => false), '/index.php'));

        // With valueless parameters.
        is('/index.php?debug', Url::addArg(array('debug' => null), '/index.php'));
        is('/index.php?debug#hash', Url::addArg(array('debug' => null), '/index.php#hash'));

        // With a URL fragment
        is('/app/admin/users?user=5#test', Url::addArg(array('user' => 5), '/app/admin/users#test'));

        // Full URL
        is('http://example.com/?a=b', Url::addArg(array('a' => 'b'), 'http://example.com'));

        // Only the query string
        is('?a=b&c=d', Url::addArg(array('c' => 'd'), '?a=b'));
        is('a=b&c=d', Url::addArg(array('c' => 'd'), 'a=b'));

        // Url encoding test
        is('/app/admin/users?param=containsa%26sym', Url::addArg(array('param' => 'containsa&sym'), '/app/admin/users'));

        // If not provided, grab the URI from the server.
        $_SERVER['REQUEST_URI'] = '/app/admin/users';
        is('/app/admin/users?user=6', Url::addArg(array('user' => 6)));
        is('/app/admin/users?user=7', Url::addArg(array('user' => 7)));
    }

    public function testRemoveArg()
    {
        is('/app/admin/users', Url::delArg('user', '/app/admin/users?user=5'));
        is('/app/admin/users?action=edit', Url::delArg('user', '/app/admin/users?action=edit&user=5'));
        is('/app/admin/users?user=5', Url::delArg(array('tab', 'action'), '/app/admin/users?action=edit&tab=personal&user=5'));
    }

    public function testHttpBuildUrl()
    {
        $url = 'http://user:pass@example.com:8080/path/?query#fragment';

        $expected = 'http://example.com/';
        $actual   = Url::buildAll($url, array(), Url::URL_STRIP_ALL);
        is($expected, $actual);

        $expected = 'http://example.com:8080/path/?query#fragment';
        $actual   = Url::buildAll($url, array(), Url::URL_STRIP_AUTH);
        is($expected, $actual);

        is('https://dev.example.com/', Url::buildAll('http://example.com/', array('scheme' => 'https', 'host' => 'dev.example.com')));
        is('http://example.com/#hi', Url::buildAll('http://example.com/', array('fragment' => 'hi'), Url::URL_REPLACE));
        is('http://example.com/page', Url::buildAll('http://example.com/', array('path' => 'page'), Url::URL_JOIN_PATH));
        is('http://example.com/page', Url::buildAll('http://example.com', array('path' => 'page'), Url::URL_JOIN_PATH));
        is('http://example.com/?hi=Bro', Url::buildAll('http://example.com/', array('query' => 'hi=Bro'), Url::URL_JOIN_QUERY));
        is('http://example.com/?show=1&hi=Bro', Url::buildAll('http://example.com/?show=1', array('query' => 'hi=Bro'), Url::URL_JOIN_QUERY));

        is('http://admin@example.com/', Url::buildAll('http://example.com/', array('user' => 'admin')));
        is('http://admin:1@example.com/', Url::buildAll('http://example.com/', array('user' => 'admin', 'pass' => '1')));
    }

    public function testPathToUrl()
    {
        $_SERVER['HTTP_HOST']     = 'test.dev';
        $_SERVER['SERVER_PORT']   = 80;
        $_SERVER['REQUEST_URI']   = '/test.php?foo=bar';
        $_SERVER['QUERY_STRING']  = 'foo=bar';
        $_SERVER['PHP_SELF']      = '/test.php';
        $_SERVER['DOCUMENT_ROOT'] = PROJECT_ROOT;

        isSame('tests/UrlTest.php', Url::pathToRel(__FILE__));
        isSame('http://test.dev/tests/UrlTest.php', Url::pathToUrl(__FILE__));

        $_SERVER['DOCUMENT_ROOT'] = str_replace('/', '\\', PROJECT_ROOT);
        isSame('tests/UrlTest.php', Url::pathToRel(__FILE__));
        isSame('http://test.dev/tests/UrlTest.php', Url::pathToUrl(__FILE__));
    }

    public function testIsAbsolute()
    {
        isTrue(Url::isAbsolute('https://site.com'));
        isTrue(Url::isAbsolute('http://site.com'));
        isTrue(Url::isAbsolute('//site.com'));
        isTrue(Url::isAbsolute('ftp://site.com'));

        isFalse(Url::isAbsolute('/path/to/file'));
        isFalse(Url::isAbsolute('w:/path/to/file'));
        isFalse(Url::isAbsolute('W:/path/to/file'));
        isFalse(Url::isAbsolute('W:\path\to\file'));
    }

    public function testEmpty()
    {
        isSame(null, Url::root());
        isSame(null, Url::path());
        isSame(null, Url::current());
    }

    public function testCreate()
    {
        isSame('http://example.com/?foo=bar', Url::create(array(
            'host'  => 'example.com',
            'user'  => '',
            'pass'  => '123456',
            'query' => array(
                'foo' => 'bar'
            )
        )));

        isSame('http://example.com/', Url::create(array(
            'host' => 'example.com',
            'part' => '',
        )));

        isSame('ssh://example.com/', Url::create(array(
            'host'   => 'example.com',
            'scheme' => 'ssh',
            'part'   => '',
        )));

        isSame('http://example.com/', Url::create(array(
            'host' => 'example.com',
            'port' => 80,
        )));

        isSame('https://example.com/', Url::create(array(
            'host' => 'example.com',
            'port' => 443,
        )));

        isSame('http://example.com/page#hash', Url::create(array(
            'host'     => 'example.com',
            'path'     => 'page',
            'fragment' => 'hash'
        )));

        isSame('https://user:123456@example.com/page?foo=bar#hash', Url::create(array(
            'scheme'   => 'https',
            'host'     => 'example.com',
            'user'     => 'user',
            'pass'     => '123456',
            'path'     => 'page',
            'query'    => array(
                'foo' => 'bar'
            ),
            'fragment' => '#hash'
        )));
    }

    public function testRootBugWithPost()
    {
        $_SERVER['HTTP_HOST']   = '127.0.0.1:8081';
        $_SERVER['SERVER_PORT'] = 8081;

        isSame('http://127.0.0.1:8081', Url::root());
    }

    public function testRootBugWithPost2()
    {
        $_SERVER['HTTP_HOST']   = '127.0.0.1:80';
        $_SERVER['SERVER_PORT'] = 80;

        isSame('http://127.0.0.1', Url::root());
    }

    public function testRootBugWithPost3()
    {
        $_SERVER['HTTP_HOST']   = '127.0.0.1';
        $_SERVER['SERVER_PORT'] = 80;

        isSame('http://127.0.0.1', Url::root());
    }
}
