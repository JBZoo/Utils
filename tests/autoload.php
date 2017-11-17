<?php
/**
 * JBZoo Utils
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Utils
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Utils
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\PHPUnit;


// @codingStandardsIgnoreFile
if (!defined('ROOT_PATH')) { // for PHPUnit process isolation
    define('ROOT_PATH', realpath('.'));
}

// main autoload
if ($autoload = realpath(ROOT_PATH . '/vendor/autoload.php')) {
    require_once $autoload;
} else {
    echo 'Please execute "composer update" !' . PHP_EOL;
    exit(1);
}


if ($fixtures = realpath(ROOT_PATH . '/tests/fixtures.php')) {
    require_once $fixtures;
}

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
function revertServerVar()
{
    unset(
        $_SERVER['HTTP_HOST'],
        $_SERVER['SERVER_PORT'],
        $_SERVER['REQUEST_URI'],
        $_SERVER['QUERY_STRING'],
        $_SERVER['PHP_SELF'],
        $_SERVER['HTTPS'],
        $_SERVER['X-FORWARDED-PROTO'],
        $_SERVER['AUTHORIZATION'],
        $_SERVER['PHP_AUTH_USER'],
        $_SERVER['PHP_AUTH_PW'],
        $_SERVER['CONTENT_TYPE'],
        $_SERVER['CONTENT_LENGTH'],
        $_SERVER['ETAG'],
        $_SERVER['HTTP_CONTENT_TYPE'],
        $_SERVER['HTTP_CONTENT_LENGTH'],
        $_SERVER['HTTP_ETAG'],
        $_SERVER['SOME_SERVER_VARIABLE'],
        $_SERVER['SOME_SERVER_VARIABLE2'],
        $_SERVER['ROOT'],
        $_SERVER['HTTP_AUTHORIZATION'],
        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'],
        $_SERVER['PHP_AUTH_DIGEST']
    );
}