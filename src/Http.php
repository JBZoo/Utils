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

namespace JBZoo\Utils;

/**
 * Class Http
 * @package JBZoo\Utils
 */
class Http
{
    /**
     * Transmit headers that force a browser to display the download file dialog.
     * Cross browser compatible. Only fires if headers have not already been sent.
     *
     * @param string      $filename The name of the filename to display to browsers
     * @param string|bool $content  The content to output for the download. If empty just the headers will be sent
     * @return boolean
     */
    public static function download($filename, $content = false)
    {
        // @codeCoverageIgnoreStart
        if (!headers_sent()) {
            // Required for some browsers
            if (Vars::bool(Sys::iniGet('zlib.output_compression'))) {
                Sys::iniSet('zlib.output_compression', 'Off');
            }

            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

            // Required for certain browsers
            header('Cache-Control: private', false);
            header('Content-Disposition: attachment; filename="' . basename(str_replace('"', '', $filename)) . '";');
            header('Content-Type: application/force-download');
            header('Content-Transfer-Encoding: binary');

            if ($content) {
                header('Content-Length: ' . strlen($content));
            }
            ob_clean();
            flush();

            if ($content) {
                echo $content;
            }

            return true;
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Sets the headers to prevent caching for the different browsers.
     * Different browsers support different nocache headers, so several
     * headers must be sent so that all of them get the point that no caching should occur
     *
     * @return boolean
     *
     * @codeCoverageIgnore
     */
    public static function nocache()
    {
        if (!headers_sent()) {
            header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');

            return true;
        }

        return false;
    }

    /**
     * Returns the IP address of the client.
     *
     * @param   boolean $trustProxy Whether or not to trust the proxy headers HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR.
     *                              ONLY use if your server is behind a proxy that sets these values
     * @return  string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function IP($trustProxy = false)
    {
        if (!$trustProxy) {
            return $_SERVER['REMOTE_ADDR'];
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];

        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ipAddress = $_SERVER['HTTP_X_REAL_IP'];

        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];

        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        return $ipAddress;
    }

    /**
     * Transmit UTF-8 content headers if the headers haven't already been sent.
     *
     * @param  string $content_type The content type to send out
     * @return boolean
     *
     * @codeCoverageIgnore
     */
    public static function utf8($content_type = 'text/html')
    {
        if (!headers_sent()) {
            header('Content-type: ' . $content_type . '; charset=utf-8');

            return true;
        }

        return false;
    }

    /**
     * Get all HTTP headers
     * @see https://github.com/symfony/http-foundation/blob/master/ServerBag.php
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getHeaders()
    {
        $headers = array();

        $contentHeaders = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);

        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } // CONTENT_* are not prefixed with HTTP_
            elseif (isset($contentHeaders[$key])) {
                $headers[$key] = $value;
            }
        }

        if (Vars::get($_SERVER['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $_SERVER['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW']   = Vars::get($_SERVER['PHP_AUTH_PW'], '');

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
            if (Vars::get($_SERVER['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

            } elseif (Vars::get($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            }

            if (null !== $authorizationHeader) {
                if (0 === stripos($authorizationHeader, 'basic ')) {
                    // Decode AUTHORIZATION header into PHP_AUTH_USER
                    // And PHP_AUTH_PW when authorization header is basic
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)), 2);

                    if (count($exploded) == 2) {
                        list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
                    }

                } elseif (empty($_SERVER['PHP_AUTH_DIGEST']) && (0 === stripos($authorizationHeader, 'digest '))) {
                    // In some circumstances PHP_AUTH_DIGEST needs to be set
                    $headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $_SERVER['PHP_AUTH_DIGEST'] = $authorizationHeader;

                } elseif (0 === stripos($authorizationHeader, 'bearer ')) {
                    /*
                     * XXX: Since there is no PHP_AUTH_BEARER in PHP predefined variables,
                     *      I'll just set $headers['AUTHORIZATION'] here.
                     *      http://php.net/manual/en/reserved.variables.server.php
                     */
                    $headers['AUTHORIZATION'] = $authorizationHeader;
                }
            }
        }

        // PHP_AUTH_USER/PHP_AUTH_PW
        if (Vars::get($headers['PHP_AUTH_USER'])) {
            $authorization = 'Basic ' . base64_encode($headers['PHP_AUTH_USER'] . ':' . $headers['PHP_AUTH_PW']);

            $headers['AUTHORIZATION'] = $authorization;

        } elseif (Vars::get($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }

        return $headers;
    }
}
