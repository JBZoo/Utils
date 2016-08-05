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
 * Class Url
 * @package JBZoo\Utils
 */
class Url
{
    /**
     * URL constants as defined in the PHP Manual under "Constants usable with http_build_url()".
     * @see http://us2.php.net/manual/en/http.constants.php#http.constants.url
     */
    const URL_REPLACE        = 1;
    const URL_JOIN_PATH      = 2;
    const URL_JOIN_QUERY     = 4;
    const URL_STRIP_USER     = 8;
    const URL_STRIP_PASS     = 16;
    const URL_STRIP_AUTH     = 32;
    const URL_STRIP_PORT     = 64;
    const URL_STRIP_PATH     = 128;
    const URL_STRIP_QUERY    = 256;
    const URL_STRIP_FRAGMENT = 512;
    const URL_STRIP_ALL      = 1024;

    const ARG_SEPARATOR = '&';

    const PORT_HTTP  = 80;
    const PORT_HTTPS = 443;

    /**
     * Add or remove query arguments to the URL.
     *
     * @param  mixed $newParams Either newkey or an associative array
     * @param  mixed $uri       URI or URL to append the queru/queries to.
     * @return string
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function addArg(array $newParams, $uri = null)
    {
        $uri = is_null($uri) ? Vars::get($_SERVER['REQUEST_URI'], '') : $uri;

        // Parse the URI into it's components
        $puri = parse_url($uri);
        if (Arr::key('query', $puri)) {
            parse_str($puri['query'], $queryParams);
            $queryParams = array_merge($queryParams, $newParams);

        } elseif (Arr::key('path', $puri) && strstr($puri['path'], '=') !== false) {
            $puri['query'] = $puri['path'];
            unset($puri['path']);
            parse_str($puri['query'], $queryParams);
            $queryParams = array_merge($queryParams, $newParams);

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
        $puri['query'] = self::build($queryParams);

        // Strip = from valueless parameters.
        $puri['query'] = preg_replace('/=(?=&|$)/', '', $puri['query']);

        // Re-construct the entire URL
        $nuri = self::buildAll($puri);

        // Make the URI consistent with our input
        foreach (array('/', '?') as $char) {
            if ($nuri[0] === $char && strstr($uri, $char) === false) {
                $nuri = substr($nuri, 1);
            }
        }

        return rtrim($nuri, '?');
    }

    /**
     * Return the current URL.
     *
     * @param bool $addAuth
     * @return string
     */
    public static function current($addAuth = false)
    {
        $current = (string)self::root($addAuth) . (string)self::path();
        return $current ? $current : null;
    }

    /**
     * Return the current path
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function path()
    {
        $url = '';

        // Get the rest of the URL
        if (!Arr::key('REQUEST_URI', $_SERVER)) {
            // Microsoft IIS doesn't set REQUEST_URI by default
            $queryString = Arr::key('QUERY_STRING', $_SERVER, true);
            if ($queryString) {
                $url .= '?' . $queryString;
            }

        } else {
            $url .= $_SERVER['REQUEST_URI'];
        }

        return $url ? $url : null;
    }

    /**
     * Return current root URL
     *
     * @param bool $addAuth
     * @return null|string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function root($addAuth = false)
    {
        $url = '';

        // Check to see if it's over https
        $isHttps = self::isHttps();

        // Was a username or password passed?
        if ($addAuth) {
            $url .= self::getAuth();
        }

        // We want the user to stay on the same host they are currently on,
        // but beware of security issues
        // see http://shiflett.org/blog/2006/mar/server-name-versus-http-host
        $host = Arr::key('HTTP_HOST', $_SERVER, true);
        $port = Arr::key('SERVER_PORT', $_SERVER, true);
        $url .= str_replace(':' . $port, '', $host);

        // Is it on a non standard port?
        if ($isHttps && ($port != self::PORT_HTTPS)) {
            $url .= Arr::key('SERVER_PORT', $_SERVER) ? ':' . $_SERVER['SERVER_PORT'] : '';

        } elseif (!$isHttps && ($port != self::PORT_HTTP)) {
            $url .= Arr::key('SERVER_PORT', $_SERVER) ? ':' . $_SERVER['SERVER_PORT'] : '';
        }

        if ($url) {
            if ($isHttps) {
                return 'https://' . $url;
            } else {
                return 'http://' . $url;
            }
        }

        return null;
    }

    /**
     * Get current auth info
     *
     * @return null|string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getAuth()
    {
        $result = null;
        $user = Arr::key('PHP_AUTH_USER', $_SERVER, true);
        if ($user) {
            $result .= $user;

            $password = Arr::key('PHP_AUTH_PW', $_SERVER, true);
            if ($password) {
                $result .= ':' . $password;
            }

            $result .= '@';
        }

        return $result;
    }

    /**
     * @param array $queryParams
     * @return string
     */
    public static function build(array $queryParams)
    {
        return http_build_query($queryParams, null, self::ARG_SEPARATOR);
    }

    /**
     * Build a URL. The parts of the second URL will be merged into the first according to the flags argument.
     * @author Jake Smith <theman@jakeasmith.com>
     * @see    https://github.com/jakeasmith/http_build_url/
     *
     * @param mixed $url    (part(s) of) an URL in form of a string or associative array like parse_url() returns
     * @param mixed $parts  same as the first argument
     * @param int   $flags  a bitmask of binary or'ed HTTP_URL constants; HTTP_URL_REPLACE is the default
     * @param array $newUrl if set, it will be filled with the parts of the composed url like parse_url() would return
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function buildAll($url, $parts = array(), $flags = self::URL_REPLACE, &$newUrl = array())
    {
        is_array($url) || $url = parse_url($url);
        is_array($parts) || $parts = parse_url($parts);

        Arr::key('query', $url) && is_string($url['query']) || $url['query'] = null;
        Arr::key('query', $parts) && is_string($parts['query']) || $parts['query'] = null;
        $keys = array('user', 'pass', 'port', 'path', 'query', 'fragment');

        // HTTP_URL_STRIP_ALL and HTTP_URL_STRIP_AUTH cover several other flags.
        if ($flags & self::URL_STRIP_ALL) {
            $flags |= self::URL_STRIP_USER
                | self::URL_STRIP_PASS
                | self::URL_STRIP_PORT
                | self::URL_STRIP_PATH
                | self::URL_STRIP_QUERY
                | self::URL_STRIP_FRAGMENT;

        } elseif ($flags & self::URL_STRIP_AUTH) {
            $flags |= self::URL_STRIP_USER
                | self::URL_STRIP_PASS;
        }

        // Schema and host are alwasy replaced
        foreach (array('scheme', 'host') as $part) {
            if (Arr::key($part, $parts)) {
                $url[$part] = $parts[$part];
            }
        }

        if ($flags & self::URL_REPLACE) {
            foreach ($keys as $key) {
                if (Arr::key($key, $parts) && $parts[$key]) {
                    $url[$key] = $parts[$key];
                }
            }

        } else {
            if (Arr::key('path', $parts) && ($flags & self::URL_JOIN_PATH)) {
                if (Arr::key('path', $url) && substr($parts['path'], 0, 1) !== '/') {
                    $url['path'] = rtrim(str_replace(basename($url['path']), '', $url['path']), '/')
                        . '/' . ltrim($parts['path'], '/');

                } else {
                    $url['path'] = $parts['path'];
                }
            }

            if (Arr::key('query', $parts) && ($flags & self::URL_JOIN_QUERY)) {
                if (Arr::key('query', $url)) {
                    parse_str($url['query'], $urlQuery);
                    parse_str($parts['query'], $partsQuery);

                    $queryParams  = array_replace_recursive($urlQuery, $partsQuery);
                    $url['query'] = self::build($queryParams);
                }
                // see deadcode else condition from utilphp lib
            }
        }

        if (Arr::key('path', $url) && substr($url['path'], 0, 1) !== '/') {
            $url['path'] = '/' . $url['path'];
        }

        foreach ($keys as $key) {
            $strip = 'URL_STRIP_' . strtoupper($key);
            if ($flags & constant(__CLASS__ . '::' . $strip)) {
                unset($url[$key]);
            }
        }

        if (Arr::key('port', $url, true) === self::PORT_HTTPS) {
            $url['scheme'] = 'https';
        } elseif (Arr::key('port', $url, true) === self::PORT_HTTP) {
            $url['scheme'] = 'http';
        }

        $parsedString = '';
        if (Arr::key('scheme', $url)) {
            $parsedString .= $url['scheme'] . '://';
        }

        if (Arr::key('user', $url)) {
            $parsedString .= $url['user'];
            if (Arr::key('pass', $url)) {
                $parsedString .= ':' . $url['pass'];
            }
            $parsedString .= '@';
        }

        if (Arr::key('host', $url)) {
            $parsedString .= $url['host'];
        }

        if (Arr::key('port', $url) && $url['port']
            && $url['port'] !== self::PORT_HTTP
            && $url['port'] !== self::PORT_HTTPS
        ) {
            $parsedString .= ':' . $url['port'];
        }

        if (!empty($url['path'])) {
            $parsedString .= $url['path'];
        } else {
            $parsedString .= '/';
        }

        if (Arr::key('query', $url) && $url['query']) {
            $parsedString .= '?' . $url['query'];
        }

        if (Arr::key('fragment', $url) && $url['fragment']) {
            $parsedString .= '#' . trim($url['fragment'], '#');
        }

        $newUrl = $url;

        return $parsedString;
    }

    /**
     * Checks to see if the page is being server over SSL or not
     *
     * @param bool $trustProxyHeaders
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function isHttps($trustProxyHeaders = false)
    {
        // Check standard HTTPS header
        if (Arr::key('HTTPS', $_SERVER)) {
            return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        }

        if ($trustProxyHeaders && Arr::key('X-FORWARDED-PROTO', $_SERVER)) {
            return $_SERVER['X-FORWARDED-PROTO'] === 'https';
        }

        // Default to not SSL
        return false;
    }

    /**
     * Removes an item or list from the query string.
     *
     * @param  string|array $keys Query key or keys to remove.
     * @param  bool         $uri  When false uses the $_SERVER value
     * @return string
     */
    public static function delArg($keys, $uri = null)
    {
        if (is_array($keys)) {
            return self::addArg(array_combine($keys, array_fill(0, count($keys), false)), $uri);
        }

        return self::addArg(array($keys => false), $uri);
    }

    /**
     * Turns all of the links in a string into HTML links.
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
     *
     * @param  string $text The string to parse
     * @return string
     */
    public static function parseLink($text)
    {
        $text = preg_replace('/&apos;/', '&#39;', $text); // IE does not handle &apos; entity!

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

        return preg_replace_callback($sectionHtmlPattern, array(__CLASS__, '_linkifyCallback'), $text);
    }

    /**
     * Callback for the preg_replace in the linkify() method.
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
     *
     * @param  string $matches Matches from the preg_ function
     * @return string
     */
    protected static function _linkifyCallback($matches)
    {
        if (isset($matches[2])) {
            return $matches[2];
        }

        return self::_linkifyRegex($matches[1]);
    }

    /**
     * Callback for the preg_replace in the linkify() method.
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
     *
     * @param  string $text Matches from the preg_ function
     * @return mixed
     */
    protected static function _linkifyRegex($text)
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

        return preg_replace($urlPattern, $urlReplace, $text);
    }

    /**
     * Convert file path to relative URL
     *
     * @param $path
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function pathToRel($path)
    {
        $root = FS::clean(Vars::get($_SERVER['DOCUMENT_ROOT']));
        $path = FS::clean($path);

        $normRoot = str_replace(DIRECTORY_SEPARATOR, '/', $root);
        $normPath = str_replace(DIRECTORY_SEPARATOR, '/', $path);

        $regExp   = '/^' . preg_quote($normRoot, '/') . '/i';
        $relative = preg_replace($regExp, '', $normPath);

        $relative = ltrim($relative, '/');

        return $relative;
    }

    /**
     * Convert file path to absolute URL
     *
     * @param $path
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function pathToUrl($path)
    {
        return self::root() . '/' . self::pathToRel($path);
    }

    /**
     * Is absolute url
     *
     * @param $path
     * @return bool
     */
    public static function isAbsolute($path)
    {
        $result = strpos($path, '//') === 0
            || preg_match('#^[a-z-]{3,}:\/\/#i', $path);

        return $result;
    }

    /**
     * @param array $parts
     * @return string
     */
    public static function create(array $parts = array())
    {
        $parts = array_merge(array(
            'scheme' => 'http',
            'query'  => array(),
        ), $parts);

        if (is_array($parts['query'])) {
            $parts['query'] = self::build($parts['query']);
        }

        return self::buildAll('', $parts, self::URL_REPLACE);
    }
}
