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

namespace JBZoo\Utils;

final class Http
{
    /**
     * Transmit headers that force a browser to display the download file dialog.
     * Cross browser compatible. Only fires if headers have not already been sent.
     * @param string $filename The name of the filename to display to browsers
     * @codeCoverageIgnore
     */
    public static function download(string $filename): bool
    {
        if (\headers_sent()) {
            return false;
        }

        // required for IE, otherwise Content-disposition is ignored
        if (isStrEmpty(Sys::iniGet('zlib.output_compression'))) {
            Sys::iniSet('zlib.output_compression', 'Off');
        }

        Sys::setTime();

        // Set headers
        \header('Pragma: public');
        \header('Expires: 0');
        \header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        \header('Cache-Control: private', false);
        \header('Content-Disposition: attachment; filename="' . \basename(\str_replace('"', '', $filename)) . '";');
        \header('Content-Type: application/force-download');
        \header('Content-Transfer-Encoding: binary');
        \header('Content-Length: ' . \filesize($filename));

        // output file
        if (Sys::isFunc('fpassthru')) {
            $handle = \fopen($filename, 'r');
            if ($handle === false) {
                throw new Exception("Can't open file '{$filename}'");
            }
            \fpassthru($handle);
            \fclose($handle);
        } else {
            /** @phan-suppress-next-line PhanPluginRemoveDebugEcho */
            echo \file_get_contents($filename);
        }

        return true;
    }

    /**
     * Sets the headers to prevent caching for the different browsers.
     * Different browsers support different nocache headers, so several
     * headers must be sent so that all of them get the point that no caching should occur.
     * @codeCoverageIgnore
     */
    public static function nocache(): bool
    {
        if (!\headers_sent()) {
            \header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
            \header('Last-Modified: ' . \gmdate('D, d M Y H:i:s') . ' GMT');
            \header('Cache-Control: no-cache, must-revalidate, max-age=0');
            \header('Pragma: no-cache');

            return true;
        }

        return false;
    }

    /**
     * Transmit UTF-8 content headers if the headers haven't already been sent.
     *
     * @param string $contentType The content type to send out
     * @codeCoverageIgnore
     */
    public static function utf8(string $contentType = 'text/html'): bool
    {
        if (!\headers_sent()) {
            \header('Content-type: ' . $contentType . '; charset=utf-8');

            return true;
        }

        return false;
    }

    /**
     * Get all HTTP headers.
     * @see https://github.com/symfony/http-foundation/blob/master/ServerBag.php
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function getHeaders(): array
    {
        $headers = [];

        $contentHeaders = ['CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true];

        foreach ($_SERVER as $key => $value) {
            if (\str_starts_with($key, 'HTTP_')) {
                $headers[\substr($key, 5)] = $value;
            } elseif (isset($contentHeaders[$key])) { // CONTENT_* are not prefixed with HTTP_
                $headers[$key] = $value;
            }
        }

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $_SERVER['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW']   = $_SERVER['PHP_AUTH_PW'] ?? '';
        } else {
            /*
             * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
             * For this workaround to work, add these lines to your .htaccess file:
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             *
             * A sample .htaccess file:
             * RewriteEngine On
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             * RewriteCond %{REQUEST_FILENAME} !-f
             * RewriteRule ^(.*)$ app.php [QSA,L]
             */
            $authorizationHeader = null;
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            }

            if ($authorizationHeader) {
                if (\stripos($authorizationHeader, 'basic ') === 0) {
                    // Decode AUTHORIZATION header into PHP_AUTH_USER
                    // and PHP_AUTH_PW when authorization header is basic
                    $exploded = \explode(
                        ':',
                        (string)\base64_decode(\substr($authorizationHeader, 6), true),
                        2,
                    );

                    $expectedNumOfParts = 2;
                    if (\count($exploded) === $expectedNumOfParts) {
                        $headers['PHP_AUTH_USER'] = $exploded[0];
                        $headers['PHP_AUTH_PW']   = $exploded[1] ?? '';
                    }
                } elseif (
                    isStrEmpty($_SERVER['PHP_AUTH_DIGEST'] ?? '')
                    && (\stripos($authorizationHeader, 'digest ') === 0)
                ) {
                    // In some circumstances PHP_AUTH_DIGEST needs to be set
                    $headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $_SERVER['PHP_AUTH_DIGEST'] = $authorizationHeader;
                } elseif (\stripos($authorizationHeader, 'bearer ') === 0) {
                    /*
                     * XXX: Since there is no PHP_AUTH_BEARER in PHP predefined variables,
                     *      I'll just set $headers['AUTHORIZATION'] here.
                     *      http://php.net/manual/en/reserved.variables.server.php
                     */
                    $headers['AUTHORIZATION'] = $authorizationHeader;
                }
            }
        }

        if (isset($headers['AUTHORIZATION'])) {
            return $headers;
        }

        // PHP_AUTH_USER/PHP_AUTH_PW
        if (isset($headers['PHP_AUTH_USER'])) {
            $user          = $headers['PHP_AUTH_USER'];
            $password      = $headers['PHP_AUTH_PW'] ?? '';
            $authorization = 'Basic ' . \base64_encode($user . ':' . $password);

            $headers['AUTHORIZATION'] = $authorization;
        } elseif (isset($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }

        return $headers;
    }
}
