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
 * Class FS
 * @package JBZoo\Utils
 */
class FS
{
    /**
     * Returns the file permissions as a nice string, like -rw-r--r-- or false if the file is not found.
     *
     * @param   string $file  The name of the file to get permissions form
     * @param   int    $perms Numerical value of permissions to display as text.
     * @return  string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function perms($file, $perms = null)
    {
        if (null === $perms) {
            if (!file_exists($file)) {
                return false;
            }

            $perms = fileperms($file);
        }

        //@codeCoverageIgnoreStart
        if (($perms & 0xC000) == 0xC000) { // Socket
            $info = 's';

        } elseif (($perms & 0xA000) == 0xA000) { // Symbolic Link
            $info = 'l';

        } elseif (($perms & 0x8000) == 0x8000) { // Regular
            $info = '-';

        } elseif (($perms & 0x6000) == 0x6000) { // Block special
            $info = 'b';

        } elseif (($perms & 0x4000) == 0x4000) { // Directory
            $info = 'd';

        } elseif (($perms & 0x2000) == 0x2000) { // Character special
            $info = 'c';

        } elseif (($perms & 0x1000) == 0x1000) { // FIFO pipe
            $info = 'p';

        } else { // Unknown
            $info = 'u';
        }
        //@codeCoverageIgnoreEnd

        // Owner
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));

        // Group
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));

        // All
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }

    /**
     * Removes a directory (and its contents) recursively.
     * Contributed by Askar (ARACOOL) <https://github.com/ARACOOOL>
     *
     * @param  string $dir              The directory to be deleted recursively
     * @param  bool   $traverseSymlinks Delete contents of symlinks recursively
     * @return bool
     * @throws \RuntimeException
     */
    public static function rmdir($dir, $traverseSymlinks = false)
    {
        if (!file_exists($dir)) {
            return true;

        } elseif (!is_dir($dir)) {
            throw new \RuntimeException('Given path is not a directory');
        }

        if (!is_link($dir) || $traverseSymlinks) {
            foreach (scandir($dir) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $currentPath = $dir . '/' . $file;

                if (is_dir($currentPath)) {
                    self::rmdir($currentPath, $traverseSymlinks);

                } elseif (!unlink($currentPath)) {
                    // @codeCoverageIgnoreStart
                    throw new \RuntimeException('Unable to delete ' . $currentPath);
                    // @codeCoverageIgnoreEnd
                }
            }
        }

        // @codeCoverageIgnoreStart
        // Windows treats removing directory symlinks identically to removing directories.
        if (is_link($dir) && !defined('PHP_WINDOWS_VERSION_MAJOR')) {
            if (!unlink($dir)) {
                throw new \RuntimeException('Unable to delete ' . $dir);
            }

        } else {
            if (!rmdir($dir)) {
                throw new \RuntimeException('Unable to delete ' . $dir);
            }
        }

        return true;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Binary safe to open file
     *
     * @param $filepath
     * @return null|string
     */
    public static function openFile($filepath)
    {
        $contents = null;

        if ($realPath = realpath($filepath)) {
            $handle   = fopen($realPath, "rb");
            $contents = fread($handle, filesize($realPath));
            fclose($handle);
        }

        return $contents;
    }

    /**
     * Quickest way for getting first file line
     *
     * @param string $filepath
     * @return string
     */
    public static function firstLine($filepath)
    {
        if (file_exists($filepath)) {
            $cacheRes  = fopen($filepath, 'r');
            $firstLine = fgets($cacheRes);
            fclose($cacheRes);

            return $firstLine;
        }

        return null;
    }

    /**
     * Set the writable bit on a file to the minimum value that allows the user running PHP to write to it.
     *
     * @param  string  $filename The filename to set the writable bit on
     * @param  boolean $writable Whether to make the file writable or not
     * @return boolean
     */
    public static function writable($filename, $writable = true)
    {
        return self::_setPerms($filename, $writable, 2);
    }

    /**
     * Set the readable bit on a file to the minimum value that allows the user running PHP to read to it.
     *
     * @param  string  $filename The filename to set the readable bit on
     * @param  boolean $readable Whether to make the file readable or not
     * @return boolean
     */
    public static function readable($filename, $readable = true)
    {
        return self::_setPerms($filename, $readable, 4);
    }

    /**
     * Set the executable bit on a file to the minimum value that allows the user running PHP to read to it.
     *
     * @param  string  $filename   The filename to set the executable bit on
     * @param  boolean $executable Whether to make the file executable or not
     * @return boolean
     */
    public static function executable($filename, $executable = true)
    {
        return self::_setPerms($filename, $executable, 1);
    }

    /**
     * Returns size of a given directory in bytes.
     *
     * @param string $dir
     * @return integer
     */
    public static function dirSize($dir)
    {
        $size = 0;

        $flags = \FilesystemIterator::CURRENT_AS_FILEINFO
            | \FilesystemIterator::SKIP_DOTS;

        $dirIter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, $flags));

        foreach ($dirIter as $key) {
            if ($key->isFile()) {
                $size += $key->getSize();
            }
        }

        return $size;
    }

    /**
     * Returns all paths inside a directory.
     *
     * @param string $dir
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function ls($dir)
    {
        $contents = array();

        $flags = \FilesystemIterator::KEY_AS_PATHNAME
            | \FilesystemIterator::CURRENT_AS_FILEINFO
            | \FilesystemIterator::SKIP_DOTS;

        $dirIter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, $flags));

        foreach ($dirIter as $path => $fi) {
            $contents[] = $path;
        }

        natsort($contents);
        return $contents;
    }

    /**
     * Nice formatting for computer sizes (Bytes).
     *
     * @param   integer $bytes    The number in bytes to format
     * @param   integer $decimals The number of decimal points to include
     * @return  string
     */
    public static function format($bytes, $decimals = 2)
    {
        $exp    = 0;
        $value  = 0;
        $symbol = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        $bytes = floatval($bytes);

        if ($bytes > 0) {
            $exp   = floor(log($bytes) / log(1024));
            $value = ($bytes / pow(1024, floor($exp)));
        }

        if ($symbol[$exp] === 'B') {
            $decimals = 0;
        }

        return number_format($value, $decimals, '.', '') . ' ' . $symbol[$exp];
    }

    /**
     * @param string $filename
     * @param bool   $isFlag
     * @param int    $perm
     * @return bool
     */
    protected static function _setPerms($filename, $isFlag, $perm)
    {
        $stat = @stat($filename);

        if ($stat === false) {
            return false;
        }

        // We're on Windows
        if (Sys::isWin()) {
            //@codeCoverageIgnoreStart
            return true;
            //@codeCoverageIgnoreEnd
        }

        list($myuid, $mygid) = array(posix_geteuid(), posix_getgid());

        $isMyUid = $stat['uid'] == $myuid;
        $isMyGid = $stat['gid'] == $mygid;

        //@codeCoverageIgnoreStart
        if ($isFlag) {
            // Set only the user writable bit (file is owned by us)
            if ($isMyUid) {
                return chmod($filename, fileperms($filename) | intval('0' . $perm . '00', 8));
            }

            // Set only the group writable bit (file group is the same as us)
            if ($isMyGid) {
                return chmod($filename, fileperms($filename) | intval('0' . $perm . $perm . '0', 8));
            }

            // Set the world writable bit (file isn't owned or grouped by us)
            return chmod($filename, fileperms($filename) | intval('0' . $perm . $perm . $perm, 8));

        } else {
            // Set only the user writable bit (file is owned by us)
            if ($isMyUid) {
                $add = intval('0' . $perm . $perm . $perm, 8);
                return self::_chmod($filename, $perm, $add);
            }

            // Set only the group writable bit (file group is the same as us)
            if ($isMyGid) {
                $add = intval('00' . $perm . $perm, 8);
                return self::_chmod($filename, $perm, $add);
            }

            // Set the world writable bit (file isn't owned or grouped by us)
            $add = intval('000' . $perm, 8);
            return self::_chmod($filename, $perm, $add);
        }
        //@codeCoverageIgnoreEnd
    }

    /**
     * Chmod alias
     *
     * @param string $filename
     * @param int    $perm
     * @param int    $add
     * @return bool
     */
    protected static function _chmod($filename, $perm, $add)
    {
        return chmod($filename, (fileperms($filename) | intval('0' . $perm . $perm . $perm, 8)) ^ $add);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function ext($path)
    {
        if (strpos($path, '?') !== false) {
            $path = preg_replace('#\?(.*)#', '', $path);
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $ext = strtolower($ext);

        return $ext;
    }

    /**
     * @param string $path
     * @return string
     */
    public static function base($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function filename($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function dirname($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function real($path)
    {
        return realpath($path);
    }

    /**
     * Function to strip additional / or \ in a path name.
     *
     * @param   string $path   The path to clean.
     * @param   string $dirSep Directory separator (optional).
     * @return  string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function clean($path, $dirSep = DIRECTORY_SEPARATOR)
    {
        if (!is_string($path) || empty($path)) {
            return '';
        }

        $path = trim((string)$path);

        if (empty($path)) {
            $path = Vars::get($_SERVER['DOCUMENT_ROOT'], '');

        } elseif (($dirSep == '\\') && ($path[0] == '\\') && ($path[1] == '\\')) {
            $path = "\\" . preg_replace('#[/\\\\]+#', $dirSep, $path);

        } else {
            $path = preg_replace('#[/\\\\]+#', $dirSep, $path);
        }

        return $path;
    }

    /**
     * Strip off the extension if it exists.
     *
     * @param string $path
     * @return string
     */
    public static function stripExt($path)
    {
        $reg  = '/\.' . preg_quote(self::ext($path)) . '$/';
        $path = preg_replace($reg, '', $path);

        return $path;
    }

    /**
     * Check is current path directory
     * @param string $path
     * @return bool
     */
    public static function isDir($path)
    {
        $path = self::clean($path);
        return is_dir($path);
    }

    /**
     * Check is current path regular file
     * @param string $path
     * @return bool
     */
    public static function isFile($path)
    {
        $path = self::clean($path);
        return file_exists($path) && is_file($path);
    }

    /**
     * Find relative path of file (remove root part)
     *
     * @param string      $filePath
     * @param string|null $rootPath
     * @param string      $forceDS
     * @param bool        $toRealpath
     * @return mixed
     */
    public static function getRelative($filePath, $rootPath = null, $forceDS = DIRECTORY_SEPARATOR, $toRealpath = true)
    {
        // Cleanup file path
        if ($toRealpath && !self::isReal($filePath)) {
            $filePath = self::real($filePath);
        }
        $filePath = self::clean($filePath, $forceDS);


        // Cleanup root path
        $rootPath = $rootPath ?: Sys::getDocRoot();
        if ($toRealpath && !self::isReal($rootPath)) {
            $rootPath = self::real($rootPath);
        }
        $rootPath = self::clean($rootPath, $forceDS);


        // Remove root part
        $relPath = preg_replace('#^' . preg_quote($rootPath) . '#i', '', $filePath);
        $relPath = ltrim($relPath, $forceDS);

        return $relPath;
    }

    /**
     * @param $path
     * @return bool
     */
    public static function isReal($path)
    {
        $expected = self::clean(self::real($path));
        $actual   = self::clean($path);

        return $expected === $actual;
    }
}
