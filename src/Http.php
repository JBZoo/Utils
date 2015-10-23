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
            if (ini_get('zlib.output_compression')) {
                @ini_set('zlib.output_compression', 'Off');
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
     */
    public static function nocache()
    {
        // @codeCoverageIgnoreStart
        if (!headers_sent()) {
            header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');

            return true;
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Returns the IP address of the client.
     *
     * @param   boolean $trustProxy Whether or not to trust the proxy headers HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR.
     *                              ONLY use if your server is behind a proxy that sets these values
     * @return  string
     */
    public static function IP($trustProxy = false)
    {
        if (!$trustProxy) {
            return $_SERVER['REMOTE_ADDR'];
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];

        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];

        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * Transmit UTF-8 content headers if the headers haven't already been sent.
     *
     * @param  string $content_type The content type to send out
     * @return boolean
     */
    public static function utf8($content_type = 'text/html')
    {
        // @codeCoverageIgnoreStart
        if (!headers_sent()) {
            header('Content-type: ' . $content_type . '; charset=utf-8');

            return true;
        }

        return false;
        // @codeCoverageIgnoreEnd
    }
}
