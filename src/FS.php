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
        $stat = @stat($filename);

        if ($stat === false) {
            return false;
        }

        // We're on Windows
        if (OS::isWin()) {
            //@codeCoverageIgnoreStart
            return true;
            //@codeCoverageIgnoreEnd
        }

        list($myuid, $mygid) = array(posix_geteuid(), posix_getgid());

        if ($writable) {
            // Set only the user writable bit (file is owned by us)
            if ($stat['uid'] == $myuid) {
                return chmod($filename, fileperms($filename) | 0200);
            }

            // Set only the group writable bit (file group is the same as us)
            if ($stat['gid'] == $mygid) {
                return chmod($filename, fileperms($filename) | 0220);
            }

            // Set the world writable bit (file isn't owned or grouped by us)
            return chmod($filename, fileperms($filename) | 0222);
        } else {
            // Set only the user writable bit (file is owned by us)
            if ($stat['uid'] == $myuid) {
                return chmod($filename, (fileperms($filename) | 0222) ^ 0222);
            }

            // Set only the group writable bit (file group is the same as us)
            if ($stat['gid'] == $mygid) {
                return chmod($filename, (fileperms($filename) | 0222) ^ 0022);
            }

            // Set the world writable bit (file isn't owned or grouped by us)
            return chmod($filename, (fileperms($filename) | 0222) ^ 0002);
        }
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
        $stat = @stat($filename);

        if ($stat === false) {
            return false;
        }

        // We're on Windows
        if (OS::isWin()) {
            //@codeCoverageIgnoreStart
            return true;
            //@codeCoverageIgnoreEnd
        }

        list($myuid, $mygid) = array(posix_geteuid(), posix_getgid());

        if ($readable) {
            // Set only the user readable bit (file is owned by us)
            if ($stat['uid'] == $myuid) {
                return chmod($filename, fileperms($filename) | 0400);
            }

            // Set only the group readable bit (file group is the same as us)
            if ($stat['gid'] == $mygid) {
                return chmod($filename, fileperms($filename) | 0440);
            }

            // Set the world readable bit (file isn't owned or grouped by us)
            return chmod($filename, fileperms($filename) | 0444);
        } else {
            // Set only the user readable bit (file is owned by us)
            if ($stat['uid'] == $myuid) {
                return chmod($filename, (fileperms($filename) | 0444) ^ 0444);
            }

            // Set only the group readable bit (file group is the same as us)
            if ($stat['gid'] == $mygid) {
                return chmod($filename, (fileperms($filename) | 0444) ^ 0044);
            }

            // Set the world readable bit (file isn't owned or grouped by us)
            return chmod($filename, (fileperms($filename) | 0444) ^ 0004);
        }
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
        $stat = @stat($filename);

        if ($stat === false) {
            return false;
        }

        // We're on Windows
        if (OS::isWin()) {
            //@codeCoverageIgnoreStart
            return true;
            //@codeCoverageIgnoreEnd
        }

        list($myuid, $mygid) = array(posix_geteuid(), posix_getgid());

        if ($executable) {
            // Set only the user readable bit (file is owned by us)
            if ($stat['uid'] == $myuid) {
                return chmod($filename, fileperms($filename) | 0100);
            }

            // Set only the group readable bit (file group is the same as us)
            if ($stat['gid'] == $mygid) {
                return chmod($filename, fileperms($filename) | 0110);
            }

            // Set the world readable bit (file isn't owned or grouped by us)
            return chmod($filename, fileperms($filename) | 0111);
        } else {
            // Set only the user readable bit (file is owned by us)
            if ($stat['uid'] == $myuid) {
                return chmod($filename, (fileperms($filename) | 0111) ^ 0111);
            }

            // Set only the group readable bit (file group is the same as us)
            if ($stat['gid'] == $mygid) {
                return chmod($filename, (fileperms($filename) | 0111) ^ 0011);
            }

            // Set the world readable bit (file isn't owned or grouped by us)
            return chmod($filename, (fileperms($filename) | 0111) ^ 0001);
        }
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

        foreach ($dirIter as $file => $key) {
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
    public static function format($bytes, $decimals = 0)
    {
        $bytes = floatval($bytes);

        if ($bytes < 1024) {
            return $bytes . ' B';

        } elseif ($bytes < pow(1024, 2)) {
            return number_format($bytes / 1024, $decimals, '.', '') . ' KiB';

        } elseif ($bytes < pow(1024, 3)) {
            return number_format($bytes / pow(1024, 2), $decimals, '.', '') . ' MiB';

        } elseif ($bytes < pow(1024, 4)) {
            return number_format($bytes / pow(1024, 3), $decimals, '.', '') . ' GiB';

        } elseif ($bytes < pow(1024, 5)) {
            return number_format($bytes / pow(1024, 4), $decimals, '.', '') . ' TiB';

        } elseif ($bytes < pow(1024, 6)) {
            return number_format($bytes / pow(1024, 5), $decimals, '.', '') . ' PiB';

        } else {
            return number_format($bytes / pow(1024, 5), $decimals, '.', '') . ' PiB';
        }
    }
}
