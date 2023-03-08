<?php

/**
 * JBZoo Toolbox - Utils.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Utils
 */

declare(strict_types=1);

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Http;

/**
 * @see     https://github.com/symfony/http-foundation/blob/master/Tests/ServerBagTest.php
 */
class HttpTest extends PHPUnit
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/autoload.php';
        revertServerVar();
    }

    public function testShouldExtractHeadersFromServerArray(): void
    {
        $this->setServerVar([
            'SOME_SERVER_VARIABLE'  => 'value',
            'SOME_SERVER_VARIABLE2' => 'value',
            'ROOT'                  => 'value',
            'HTTP_CONTENT_TYPE'     => 'text/html',
            'HTTP_CONTENT_LENGTH'   => '0',
            'HTTP_ETAG'             => 'asdf',
            'PHP_AUTH_USER'         => 'foo',
            'PHP_AUTH_PW'           => 'bar',
        ]);

        is([
            'CONTENT_TYPE'   => 'text/html',
            'CONTENT_LENGTH' => '0',
            'ETAG'           => 'asdf',
            'AUTHORIZATION'  => 'Basic ' . \base64_encode('foo:bar'),
            'PHP_AUTH_USER'  => 'foo',
            'PHP_AUTH_PW'    => 'bar',
        ], Http::getHeaders());
    }

    public function testHttpPasswordIsOptional(): void
    {
        $this->setServerVar(['PHP_AUTH_USER' => 'foo']);

        is([
            'AUTHORIZATION' => 'Basic ' . \base64_encode('foo:'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => '',
        ], Http::getHeaders());
    }

    public function testHttpBasicAuthWithPhpCgi(): void
    {
        $this->setServerVar(['HTTP_AUTHORIZATION' => 'Basic ' . \base64_encode('foo:bar')]);

        is([
            'AUTHORIZATION' => 'Basic ' . \base64_encode('foo:bar'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => 'bar',
        ], Http::getHeaders());
    }

    public function testHttpBasicAuthWithPhpCgiBogus(): void
    {
        $this->setServerVar(['HTTP_AUTHORIZATION' => 'Basic_' . \base64_encode('foo:bar')]);

        // Username and passwords should not be set as the header is bogus
        $headers = Http::getHeaders();
        isFalse(isset($headers['PHP_AUTH_USER']));
        isFalse(isset($headers['PHP_AUTH_PW']));
    }

    public function testHttpBasicAuthWithPhpCgiRedirect(): void
    {
        $this->setServerVar(['REDIRECT_HTTP_AUTHORIZATION' => 'Basic ' . \base64_encode('username:pass:word')]);

        is([
            'AUTHORIZATION' => 'Basic ' . \base64_encode('username:pass:word'),
            'PHP_AUTH_USER' => 'username',
            'PHP_AUTH_PW'   => 'pass:word',
        ], Http::getHeaders());
    }

    public function testHttpBasicAuthWithPhpCgiEmptyPassword(): void
    {
        $this->setServerVar(['HTTP_AUTHORIZATION' => 'Basic ' . \base64_encode('foo:')]);

        is([
            'AUTHORIZATION' => 'Basic ' . \base64_encode('foo:'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => '',
        ], Http::getHeaders());
    }

    public function testHttpDigestAuthWithPhpCgi(): void
    {
        $digest = 'Digest username="foo", realm="acme", nonce="' . \md5('secret') . '", uri="/protected, qop="auth"';
        $this->setServerVar(['HTTP_AUTHORIZATION' => $digest]);

        is([
            'AUTHORIZATION'   => $digest,
            'PHP_AUTH_DIGEST' => $digest,
        ], Http::getHeaders());
    }

    public function testHttpDigestAuthWithPhpCgiBogus(): void
    {
        $digest = 'Digest_username="foo", realm="acme", nonce="' . \md5('secret') . '", uri="/protected, qop="auth"';
        $this->setServerVar(['HTTP_AUTHORIZATION' => $digest]);

        // Username and passwords should not be set as the header is bogus
        $headers = Http::getHeaders();
        isFalse(isset($headers['PHP_AUTH_USER']));
        isFalse(isset($headers['PHP_AUTH_PW']));
    }

    public function testHttpDigestAuthWithPhpCgiRedirect(): void
    {
        $digest = 'Digest username="foo", realm="acme", nonce="' . \md5('secret') . '", uri="/protected, qop="auth"';
        $this->setServerVar(['REDIRECT_HTTP_AUTHORIZATION' => $digest]);

        is([
            'AUTHORIZATION'   => $digest,
            'PHP_AUTH_DIGEST' => $digest,
        ], Http::getHeaders());
    }

    public function testOAuthBearerAuth(): void
    {
        $headerContent = 'Bearer L-yLEOr9zhmUYRkzN1jwwxwQ-PBNiKDc8dgfB4hTfvo';
        $this->setServerVar(['HTTP_AUTHORIZATION' => $headerContent]);

        is([
            'AUTHORIZATION' => $headerContent,
        ], Http::getHeaders());
    }

    public function testOAuthBearerAuthWithRedirect(): void
    {
        $headerContent = 'Bearer L-yLEOr9zhmUYRkzN1jwwxwQ-PBNiKDc8dgfB4hTfvo';
        $this->setServerVar(['REDIRECT_HTTP_AUTHORIZATION' => $headerContent]);

        is([
            'AUTHORIZATION' => $headerContent,
        ], Http::getHeaders());
    }

    /**
     * @see https://github.com/symfony/symfony/issues/17345
     */
    public function testItDoesNotOverwriteTheAuthorizationHeaderIfItIsAlreadySet(): void
    {
        $headerContent = 'Bearer L-yLEOr9zhmUYRkzN1jwwxwQ-PBNiKDc8dgfB4hTfvo';
        $this->setServerVar([
            'PHP_AUTH_USER'      => 'foo',
            'HTTP_AUTHORIZATION' => $headerContent,
        ]);

        is([
            'AUTHORIZATION' => $headerContent,
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => '',
        ], Http::getHeaders());
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function setServerVar(array $vars): void
    {
        revertServerVar();

        foreach ($vars as $key => $value) {
            $_SERVER[$key] = $value;
        }
    }
}
