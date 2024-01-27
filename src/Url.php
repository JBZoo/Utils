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

use function JBZoo\Data\data;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class Url
{
    /**
     * URL constants as defined in the PHP Manual under "Constants usable with http_build_url()".
     * @see http://us2.php.net/manual/en/http.constants.php#http.constants.url
     */
    public const URL_REPLACE        = 1;
    public const URL_JOIN_PATH      = 2;
    public const URL_JOIN_QUERY     = 4;
    public const URL_STRIP_USER     = 8;
    public const URL_STRIP_PASS     = 16;
    public const URL_STRIP_AUTH     = 32;
    public const URL_STRIP_PORT     = 64;
    public const URL_STRIP_PATH     = 128;
    public const URL_STRIP_QUERY    = 256;
    public const URL_STRIP_FRAGMENT = 512;
    public const URL_STRIP_ALL      = 1024;

    public const ARG_SEPARATOR = '&';

    public const PORT_HTTP  = 80;
    public const PORT_HTTPS = 443;

    /**
     * Add or remove query arguments to the URL.
     * @param array       $newParams Either new key or an associative array
     * @param null|string $uri       URI or URL to append the query/queries to
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function addArg(array $newParams, ?string $uri = null): string
    {
        $uri ??= ($_SERVER['REQUEST_URI'] ?? '');

        // Parse the URI into it's components
        $parsedUri = data((array)\parse_url($uri));

        $parsedQuery = $parsedUri->getString('query');
        $parsedPath  = $parsedUri->getString('path');

        if (!isStrEmpty($parsedQuery)) {
            \parse_str($parsedQuery, $queryParams);
            $queryParams = \array_merge($queryParams, $newParams);
        } elseif (!isStrEmpty($parsedPath) && \str_contains($parsedPath, '=')) {
            $parsedUri['query'] = $parsedUri['path'];
            $parsedUri->remove('path');
            \parse_str((string)$parsedUri['query'], $queryParams);
            $queryParams = \array_merge($queryParams, $newParams);
        } else {
            $queryParams = $newParams;
        }

        // Strip out any query params that are set to false.
        // Properly handle valueless parameters.
        foreach ($queryParams as $param => $value) {
            if ($value === false) {
                unset($queryParams[$param]);
            } elseif ($value === null) {
                $queryParams[$param] = '';
            }
        }

        // Re-construct the query string
        $parsedUri['query'] = self::build($queryParams);

        // Strip = from valueless parameters.
        $parsedUri['query'] = (string)\preg_replace('/=(?=&|$)/', '', (string)$parsedUri['query']);

        // Re-construct the entire URL
        $newUri = self::buildAll((array)$parsedUri);

        // Make the URI consistent with our input
        foreach ([':', '/', '?'] as $char) {
            if ($newUri[0] === $char && !\str_contains($uri, $char)) {
                $newUri = \substr($newUri, 1);
            }
        }

        return \rtrim($newUri, '?');
    }

    /**
     * Returns the current URL.
     */
    public static function current(bool $addAuth = false): ?string
    {
        $root   = self::root($addAuth);
        $path   = self::path();
        $result = \trim("{$root}{$path}");

        return $result === '' ? null : $result;
    }

    /**
     * Returns the current path.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function path(): ?string
    {
        $url = '';

        // Get the rest of the URL
        if (!\array_key_exists('REQUEST_URI', $_SERVER)) {
            // Microsoft IIS doesn't set REQUEST_URI by default
            $queryString = $_SERVER['QUERY_STRING'] ?? null;
            if ($queryString !== null) {
                $url .= '?' . $queryString;
            }
        } else {
            $url .= $_SERVER['REQUEST_URI'];
        }

        return $url === '' ? null : $url;
    }

    /**
     * Returns current root URL.
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function root(bool $addAuth = false): ?string
    {
        $url = '';

        // Check to see if it's over https
        $isHttps = self::isHttps();

        // Was a username or password passed?
        if ($addAuth) {
            $url .= self::getAuth() ?? '';
        }

        $serverData = data($_SERVER);

        // We want the user to stay on the same host they are currently on,
        // but beware of security issues
        // see http://shiflett.org/blog/2006/mar/server-name-versus-http-host
        $host = (string)$serverData->get('HTTP_HOST');
        $port = (int)$serverData->get('SERVER_PORT');
        $url .= \str_replace(':' . $port, '', $host);

        // Is it on a non-standard port?
        if ($isHttps && $port !== self::PORT_HTTPS) {
            $url .= $port > 0 ? ":{$port}" : '';
        } elseif (!$isHttps && $port !== self::PORT_HTTP) {
            $url .= $port > 0 ? ":{$port}" : '';
        }

        if (!isStrEmpty($url)) {
            if ($isHttps) {
                return 'https://' . $url;
            }

            /** @noinspection HttpUrlsUsage */
            return 'http://' . $url;
        }

        return null;
    }

    /**
     * Get current auth info.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getAuth(): ?string
    {
        $result = null;

        $user = $_SERVER['PHP_AUTH_USER'] ?? '';

        if ($user !== '') {
            $result   = $user;
            $password = $_SERVER['PHP_AUTH_PW'] ?? '';

            if ($password !== '') {
                $result .= ':' . $password;
            }

            $result .= '@';
        }

        return $result;
    }

    /**
     * Builds HTTP query from array.
     */
    public static function build(array $queryParams): string
    {
        return \http_build_query($queryParams, '', self::ARG_SEPARATOR);
    }

    /**
     * Build a URL. The parts of the second URL will be merged into the first according to the flags' argument.
     *
     * @param array|string $sourceUrl (part(s) of) a URL in form of a string
     *                                or associative array like parse_url() returns
     * @param array|string $destParts Same as the first argument
     * @param int          $flags     A bitmask of binary or HTTP_URL constants; HTTP_URL_REPLACE is the default
     * @param array        $newUrl    If set, it will be filled with the parts of the composed url like parse_url()
     *                                would return
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     *
     * @see    https://github.com/jakeasmith/http_build_url/
     * @author Jake Smith <theman@jakeasmith.com>
     */
    public static function buildAll(
        array|string $sourceUrl,
        array|string $destParts = [],
        int $flags = self::URL_REPLACE,
        array &$newUrl = [],
    ): string {
        if (!\is_array($sourceUrl)) {
            $sourceUrl = \parse_url($sourceUrl);
        }

        if (!\is_array($destParts)) {
            $destParts = \parse_url($destParts);
        }

        $url     = data((array)$sourceUrl);
        $parts   = data((array)$destParts);
        $allKeys = ['user', 'pass', 'port', 'path', 'query', 'fragment'];

        // HTTP_URL_STRIP_ALL and HTTP_URL_STRIP_AUTH cover several other flags.
        if (($flags & self::URL_STRIP_ALL) > 0) {
            $flags |= self::URL_STRIP_USER | self::URL_STRIP_PASS | self::URL_STRIP_PORT | self::URL_STRIP_PATH
                | self::URL_STRIP_QUERY | self::URL_STRIP_FRAGMENT;
        } elseif (($flags & self::URL_STRIP_AUTH) > 0) {
            $flags |= self::URL_STRIP_USER | self::URL_STRIP_PASS;
        }

        // Schema and host are always replaced
        if ($parts->has('scheme')) {
            $url['scheme'] = $parts->get('scheme');
        }

        if ($parts->has('host')) {
            $url['host'] = $parts->get('host');
        }

        if (($flags & self::URL_REPLACE) > 0) {
            foreach ($allKeys as $key) {
                if ($parts->has($key)) {
                    $url[$key] = $parts->get($key);
                }
            }
        } else {
            // PATH
            if (($flags & self::URL_JOIN_PATH) > 0 && $parts->has('path')) {
                if ($url->has('path') && $parts->get('path')[0] !== '/') {
                    $url['path'] = \rtrim(\str_replace(\basename((string)$url['path']), '', (string)$url['path']), '/')
                        . '/'
                        . \ltrim((string)$parts['path'], '/');
                } else {
                    $url['path'] = $parts['path'];
                }
            }

            // QUERY
            if (($flags & self::URL_JOIN_QUERY) > 0 && $parts->has('query')) {
                \parse_str($url->get('query', ''), $urlQuery);
                \parse_str($parts->get('query', ''), $partsQuery);

                $queryParams  = \array_replace_recursive($urlQuery, $partsQuery);
                $url['query'] = self::build($queryParams);
            }
        }

        $urlPath = $url->getString('path');
        if (!isStrEmpty($urlPath)) {
            $url['path'] = '/' . \ltrim($urlPath, '/');
        }

        foreach ($allKeys as $key) {
            $strip = 'URL_STRIP_' . \strtoupper($key);
            if (($flags & \constant(__CLASS__ . '::' . $strip)) > 0) {
                $url->remove($key);
            }
        }

        if ($url->get('port', null, 'int') === self::PORT_HTTPS) {
            $url['scheme'] = 'https';
        } elseif ($url->get('port', null, 'int') === self::PORT_HTTP) {
            $url['scheme'] = 'http';
        }

        if ($url->getInt('port') === 0) {
            if ($url->is('scheme', 'https')) {
                $url['port'] = 443;
            } elseif ($url->is('scheme', 'http')) {
                $url['port'] = 80;
            }
        }

        $parsedString = $url->has('scheme') ? ($url['scheme'] . '://') : '';

        if ($url->getString('user') !== '') {
            $parsedString .= $url['user'];
            $parsedString .= $url->getString('pass') === '' ? '' : (':' . $url->getString('pass'));
            $parsedString .= '@';
        }

        $parsedString .= $url->has('host') ? $url['host'] : '';

        if ((int)$url->get('port') !== self::PORT_HTTP && $url->get('scheme') === 'http') {
            $parsedString .= ':' . $url['port'];
        }

        if ($url->getString('path') !== '') {
            $parsedString .= $url['path'];
        } else {
            $parsedString .= '/';
        }

        if ($url->getString('query') !== '') {
            $parsedString .= '?' . $url->getString('query');
        }

        if ($url->getString('fragment') !== '') {
            $parsedString .= '#' . \trim($url->getString('fragment'), '#');
        }

        $newUrl = $url->getArrayCopy();

        return $parsedString;
    }

    /**
     * Checks to see if the page is being server over SSL or not.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function isHttps(bool $trustProxyHeaders = false): bool
    {
        // Check standard HTTPS header
        if (\array_key_exists('HTTPS', $_SERVER)) {
            return !isStrEmpty($_SERVER['HTTPS'] ?? '') && $_SERVER['HTTPS'] !== 'off';
        }

        if ($trustProxyHeaders && \array_key_exists('X-FORWARDED-PROTO', $_SERVER)) {
            return $_SERVER['X-FORWARDED-PROTO'] === 'https';
        }

        // Default is not SSL
        return false;
    }

    /**
     * Removes an item or list from the query string.
     * @param array|string $keys query key or keys to remove
     * @param null|string  $uri  When null uses the $_SERVER value
     */
    public static function delArg(array|string $keys, ?string $uri = null): string
    {
        if (\is_array($keys)) {
            $params = \array_combine($keys, \array_fill(0, \count($keys), false));

            return self::addArg($params, (string)$uri);
        }

        return self::addArg([$keys => false], (string)$uri);
    }

    /**
     * Turns all the links in a string into HTML links.
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>.
     * @param string $text The string to parse
     */
    public static function parseLink(string $text): string
    {
        $text = (string)\preg_replace('/&apos;/', '&#39;', $text); // IE does not handle &apos; entity!

        $sectionHtmlPattern = '%            # Rev:20100913_0900 github.com/jmrware/LinkifyURL
                                            # Section text into HTML <A> tags  and everything else.
             (                              # $1: Everything not HTML <A> tag.
               [^<]+(?:(?!<a\b)<[^<]*)*     # non A tag stuff starting with non-"<".
               | (?:(?!<a\b)<[^<]*)+        # non A tag stuff starting with "<".
             )                              # End $1.
             | (                            # $2: HTML <A...>...</A> tag.
                 <a\b[^>]*>                 # <A...> opening tag.
                 [^<]*(?:(?!</a\b)<[^<]*)*  # A tag contents.
                 </a\s*>                    # </A> closing tag.
             )                              # End $2:
             %ix';

        return (string)\preg_replace_callback(
            $sectionHtmlPattern,
            static fn (array $matches): string => self::linkifyCallback($matches),
            $text,
        );
    }

    /**
     * Convert file path to relative URL.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function pathToRel(string $path): string
    {
        $root = FS::clean($_SERVER['DOCUMENT_ROOT'] ?? null);
        $path = FS::clean($path);

        $normRoot = \str_replace(\DIRECTORY_SEPARATOR, '/', $root);
        $normPath = \str_replace(\DIRECTORY_SEPARATOR, '/', $path);

        $regExp   = '/^' . \preg_quote($normRoot, '/') . '/i';
        $relative = (string)\preg_replace($regExp, '', $normPath);

        $relative = \ltrim($relative, '/');

        return $relative;
    }

    /**
     * Convert file path to absolute URL.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function pathToUrl(string $path): string
    {
        $root = self::root();
        $rel  = self::pathToRel($path);

        return "{$root}/{$rel}";
    }

    /**
     * Check if URL is not relative.
     */
    public static function isAbsolute(string $path): bool
    {
        return \str_starts_with($path, '//') || \preg_match('#^[a-z-]{3,}://#i', $path) > 0;
    }

    /**
     * Create URL from array params.
     */
    public static function create(array $parts = []): string
    {
        $parts = \array_merge([
            'scheme' => 'https',
            'query'  => [],
        ], $parts);

        if (\is_array($parts['query'])) {
            $parts['query'] = self::build($parts['query']);
        }

        return self::buildAll('', $parts, self::URL_REPLACE);
    }

    /**
     * Callback for the preg_replace in the linkify() method.
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>.
     * @param array $matches Matches from the preg_ function
     */
    private static function linkifyCallback(array $matches): string
    {
        return $matches[2] ?? self::linkifyRegex($matches[1]);
    }

    /**
     * Callback for the preg_replace in the linkify() method.
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>.
     * @param string $text Matches from the preg_ function
     */
    private static function linkifyRegex(string $text): string
    {
        $urlPattern = '/                                            # Rev:20100913_0900 github.com\/jmrware\/LinkifyURL
                                                                    # Match http & ftp URL that is not already linkified
                                                                    # Alternative 1: URL delimited by (parentheses).
            (\()                                                    # $1 "(" start delimiter.
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $2: URL.
            (\))                                                    # $3: ")" end delimiter.
            |                                                       # Alternative 2: URL delimited by [square brackets].
            (\[)                                                    # $4: "[" start delimiter.
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $5: URL.
            (\])                                                    # $6: "]" end delimiter.
            |                                                       # Alternative 3: URL delimited by {curly braces}.
            (\{)                                                    # $7: "{" start delimiter.
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $8: URL.
            (\})                                                    # $9: "}" end delimiter.
            |                                                       # Alternative 4: URL delimited by <angle brackets>.
            (<|&(?:lt|\#60|\#x3c);)                                 # $10: "<" start delimiter (or HTML entity).
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $11: URL.
            (>|&(?:gt|\#62|\#x3e);)                                 # $12: ">" end delimiter (or HTML entity).
            |                                                       # Alt. 5: URL not delimited by (), [], {} or <>.
            (                                                       # $13: Prefix proving URL not already linked.
            (?: ^                                                   # Can be a beginning of line or string, or
             | [^=\s\'"\]]                                          # a non-"=", non-quote, non-"]", followed by
            ) \s*[\'"]?                                             # optional whitespace and optional quote;
              | [^=\s]\s+                                           # or... a non-equals sign followed by whitespace.
            )                                                       # End $13. Non-prelinkified-proof prefix.
            (\b                                                     # $14: Other non-delimited URL.
            (?:ht|f)tps?:\/\/                                       # Required literal http, https, ftp or ftps prefix.
            [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]+                     # All URI chars except "&" (normal*).
            (?:                                                     # Either on a "&" or at the end of URI.
            (?!                                                     # Allow a "&" char only if not start of an...
            &(?:gt|\#0*62|\#x0*3e);                                 # HTML ">" entity, or
            | &(?:amp|apos|quot|\#0*3[49]|\#x0*2[27]);              # a [&\'"] entity if
            [.!&\',:?;]?                                            # followed by optional punctuation then
            (?:[^a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]|$)              # a non-URI char or EOS.
           ) &                                                      # If neg-assertion true, match "&" (special).
            [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]*                     # More non-& URI chars (normal*).
           )*                                                       # Unroll-the-loop (special normal*)*.
            [a-z0-9\-_~$()*+=\/#[\]@%]                              # Last char can\'t be [.!&\',;:?]
           )                                                        # End $14. Other non-delimited URL.
            /imx';

        $urlReplace = '$1$4$7$10$13<a href="$2$5$8$11$14">$2$5$8$11$14</a>$3$6$9$12';

        return (string)\preg_replace($urlPattern, $urlReplace, $text);
    }
}
