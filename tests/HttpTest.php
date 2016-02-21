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

use JBZoo\Utils\Http;

/**
 * Class HttpTest
 * @package JBZoo\PHPUnit
 * @see     https://github.com/symfony/http-foundation/blob/master/Tests/ServerBagTest.php
 */
class HttpTest extends PHPUnit
{
    /**
     * @param array $vars
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function _setServerVar(array $vars)
    {
        foreach ($vars as $key => $value) {
            $_SERVER[$key] = $value;
        }
    }

    public function testShouldExtractHeadersFromServerArray()
    {
        $this->_setServerVar(array(
            'SOME_SERVER_VARIABLE'  => 'value',
            'SOME_SERVER_VARIABLE2' => 'value',
            'ROOT'                  => 'value',
            'HTTP_CONTENT_TYPE'     => 'text/html',
            'HTTP_CONTENT_LENGTH'   => '0',
            'HTTP_ETAG'             => 'asdf',
            'PHP_AUTH_USER'         => 'foo',
            'PHP_AUTH_PW'           => 'bar',
        ));

        is(array(
            'CONTENT_TYPE'   => 'text/html',
            'CONTENT_LENGTH' => '0',
            'ETAG'           => 'asdf',
            'AUTHORIZATION'  => 'Basic ' . base64_encode('foo:bar'),
            'PHP_AUTH_USER'  => 'foo',
            'PHP_AUTH_PW'    => 'bar',
        ), Http::getHeaders());
    }

    public function testHttpPasswordIsOptional()
    {
        $this->_setServerVar(array('PHP_AUTH_USER' => 'foo'));

        is(array(
            'AUTHORIZATION' => 'Basic ' . base64_encode('foo:'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => '',
        ), Http::getHeaders());
    }

    public function testHttpBasicAuthWithPhpCgi()
    {
        $this->_setServerVar(array('HTTP_AUTHORIZATION' => 'Basic ' . base64_encode('foo:bar')));

        is(array(
            'AUTHORIZATION' => 'Basic ' . base64_encode('foo:bar'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => 'bar',
        ), Http::getHeaders());
    }

    public function testHttpBasicAuthWithPhpCgiBogus()
    {
        $this->_setServerVar(array('HTTP_AUTHORIZATION' => 'Basic_' . base64_encode('foo:bar')));

        // Username and passwords should not be set as the header is bogus
        $headers = Http::getHeaders();
        isFalse(isset($headers['PHP_AUTH_USER']));
        isFalse(isset($headers['PHP_AUTH_PW']));
    }

    public function testHttpBasicAuthWithPhpCgiRedirect()
    {
        $this->_setServerVar(array('REDIRECT_HTTP_AUTHORIZATION' => 'Basic ' . base64_encode('username:pass:word')));

        is(array(
            'AUTHORIZATION' => 'Basic ' . base64_encode('username:pass:word'),
            'PHP_AUTH_USER' => 'username',
            'PHP_AUTH_PW'   => 'pass:word',
        ), Http::getHeaders());
    }

    public function testHttpBasicAuthWithPhpCgiEmptyPassword()
    {
        $this->_setServerVar(array('HTTP_AUTHORIZATION' => 'Basic ' . base64_encode('foo:')));

        is(array(
            'AUTHORIZATION' => 'Basic ' . base64_encode('foo:'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => '',
        ), Http::getHeaders());
    }

    public function testHttpDigestAuthWithPhpCgi()
    {
        $digest = 'Digest username="foo", realm="acme", nonce="' . md5('secret') . '", uri="/protected, qop="auth"';
        $this->_setServerVar(array('HTTP_AUTHORIZATION' => $digest));

        is(array(
            'AUTHORIZATION'   => $digest,
            'PHP_AUTH_DIGEST' => $digest,
        ), Http::getHeaders());
    }

    public function testHttpDigestAuthWithPhpCgiBogus()
    {
        $digest = 'Digest_username="foo", realm="acme", nonce="' . md5('secret') . '", uri="/protected, qop="auth"';
        $this->_setServerVar(array('HTTP_AUTHORIZATION' => $digest));

        // Username and passwords should not be set as the header is bogus
        $headers = Http::getHeaders();
        isFalse(isset($headers['PHP_AUTH_USER']));
        isFalse(isset($headers['PHP_AUTH_PW']));
    }

    public function testHttpDigestAuthWithPhpCgiRedirect()
    {
        $digest = 'Digest username="foo", realm="acme", nonce="' . md5('secret') . '", uri="/protected, qop="auth"';
        $this->_setServerVar(array('REDIRECT_HTTP_AUTHORIZATION' => $digest));

        is(array(
            'AUTHORIZATION'   => $digest,
            'PHP_AUTH_DIGEST' => $digest,
        ), Http::getHeaders());
    }

    public function testOAuthBearerAuth()
    {
        $headerContent = 'Bearer L-yLEOr9zhmUYRkzN1jwwxwQ-PBNiKDc8dgfB4hTfvo';
        $this->_setServerVar(array('HTTP_AUTHORIZATION' => $headerContent));

        is(array(
            'AUTHORIZATION' => $headerContent,
        ), Http::getHeaders());
    }

    public function testOAuthBearerAuthWithRedirect()
    {
        $headerContent = 'Bearer L-yLEOr9zhmUYRkzN1jwwxwQ-PBNiKDc8dgfB4hTfvo';
        $this->_setServerVar(array('REDIRECT_HTTP_AUTHORIZATION' => $headerContent));

        is(array(
            'AUTHORIZATION' => $headerContent,
        ), Http::getHeaders());
    }

    /**
     * @see https://github.com/symfony/symfony/issues/17345
     */
    public function testItDoesNotOverwriteTheAuthorizationHeaderIfItIsAlreadySet()
    {
        $headerContent = 'Bearer L-yLEOr9zhmUYRkzN1jwwxwQ-PBNiKDc8dgfB4hTfvo';
        $this->_setServerVar(array(
            'PHP_AUTH_USER'      => 'foo',
            'HTTP_AUTHORIZATION' => $headerContent,
        ));

        is(array(
            'AUTHORIZATION' => $headerContent,
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => '',
        ), Http::getHeaders());
    }
}
