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

use JBZoo\Utils\FS;
use JBZoo\Utils\OS;
use JBZoo\Utils\Vars;

/**
 * Class FileSystemTest
 * @package JBZoo\PHPUnit
 */
class FileSystemTest extends PHPUnit
{

    public function test()
    {
        //incomplete();
    }


    public function testRemoveDir()
    {
        $dirname = dirname(__FILE__);

        // Test deleting a non-existant directory
        isFalse(file_exists($dirname . '/test1'));
        isTrue(FS::rmdir($dirname . '/test1'));

        // Test deleting an empty directory
        $dir = $dirname . '/test2';
        mkdir($dir);

        isTrue(is_dir($dir));

        if (is_dir($dir)) {
            FS::rmdir($dir);
            isFalse(is_dir($dir));
        }

        // Test deleting a non-empty directory
        $dir  = $dirname . '/test3';
        $file = $dirname . '/test3/test.txt';
        mkdir($dir);
        touch($file);

        isTrue(is_dir($dir));
        isTrue(is_file($file));

        if (is_dir($dir)) {
            FS::rmdir($dir);
            isFalse(is_dir($dir));
            isFalse(is_file($file));
        }

        // Test deleting a non-directory path
        $file = $dirname . '/test4.txt';
        touch($file);

        try {
            FS::rmdir($file);
            isTrue(false);
        } catch (\Exception $e) {
            isTrue(true);
        }

        unlink($file);

        // Test deleting a nested directory
        $dir1  = $dirname . '/test5';
        $dir2  = $dirname . '/test5/nested_dir';
        $file1 = $dir1 . '/file1.txt';
        $file2 = $dir2 . '/file2.txt';
        mkdir($dir1);
        mkdir($dir2);
        touch($file1);
        touch($file2);

        isTrue(is_dir($dir1));
        isTrue(is_dir($dir2));
        isTrue(is_file($file1));
        isTrue(is_file($file2));

        if (is_dir($dir1)) {
            FS::rmdir($dir1);
            isFalse(is_dir($dir1));
            isFalse(is_dir($dir2));
            isFalse(is_file($file1));
            isFalse(is_file($file2));
        }

        // Test symlink traversal.
        if (OS::isWin()) {
            skip('Windows does not correctly support symlinks :(');

        } else {
            $dir       = $dirname . '/test6';
            $nestedDir = "$dir/nested";
            $symlink   = "$dir/nested-symlink";
            @mkdir($dir);
            @mkdir($nestedDir);

            $symlinkStatus = symlink($nestedDir, $symlink);
            isTrue($symlinkStatus, 'The test system does not support making symlinks.');

            if (!$symlink) {
                return;
            }

            isTrue(FS::rmdir($symlink, true), 'Could not delete a symlinked directory.');
            isFalse(file_exists($symlink), 'Could not delete a symlinked directory.');

            FS::rmdir($dir, true);
            isFalse(is_dir($dir), 'Could not delete a directory with a symlinked directory inside of it.');
        }
    }

    public function testOpenFile()
    {
        isContain('FS::openFile(', FS::openFile(__FILE__));
    }

    public function testFirstLine()
    {
        isContain('<?php', FS::firstLine(__FILE__));
        isNull(FS::firstLine(__FILE__ . '_noexists'));
    }

    public function testPerms()
    {
        isFalse(FS::perms('/no/such/file'));
    }

    public function testWritable()
    {
        if (OS::isWin()) {
            skip('This functionality is not working on Windows.');
        }

        if (OS::isRoot()) {
            skip('These tests don\'t work when run as root');
        }

        isFalse(FS::writable('/no/such/file'));

        // Create a file to test with
        $dirname = dirname(__FILE__);
        $file    = $dirname . '/test7';
        touch($file);
        chmod($file, 0644);

        // The file is owned by us so it should be writable
        isTrue(is_writable($file));
        is('-rw-r--r--', FS::perms($file));

        // Toggle writable bit off for us
        FS::writable($file, false);
        clearstatcache();
        isFalse(is_writable($file));
        is('-r--r--r--', FS::perms($file));

        // Toggle writable bit back on for us
        FS::writable($file, true);
        clearstatcache();
        isTrue(is_writable($file));
        is('-rw-r--r--', FS::perms($file));

        unlink($file);
    }

    public function testReadable()
    {
        if (OS::isWin()) {
            skip('This functionality is not working on Windows.');
        }

        if (OS::isRoot()) {
            skip('These tests don\'t work when run as root');
        }

        isFalse(FS::readable('/no/such/file'));

        $dirname = dirname(__FILE__);
        $file    = $dirname . '/test8';
        touch($file);

        isTrue(is_readable($file));

        FS::readable($file, false);
        clearstatcache();
        isFalse(is_readable($file));

        FS::readable($file, true);
        clearstatcache();
        isTrue(is_readable($file));

        unlink($file);
    }

    public function testExecutable()
    {
        if (OS::isWin()) {
            skip('This functionality is not working on Windows.');
        }

        if (OS::isRoot()) {
            skip('These tests don\'t work when run as root');
        }

        isFalse(FS::executable('/no/such/file'));

        $dirname = dirname(__FILE__);
        $file    = $dirname . '/test9';
        touch($file);

        isFalse(is_executable($file));

        FS::executable($file, true);
        clearstatcache();
        isTrue(is_executable($file));

        FS::executable($file, false);
        clearstatcache();
        isFalse(is_executable($file));

        unlink($file);
    }

    public function testGetHome()
    {
        // Test for OS Default.
        isTrue(is_writable(OS::getHome()));

        $oldServer = $_SERVER;
        unset($_SERVER);

        // Test for UNIX.
        $_SERVER['HOME'] = '/home/unknown';
        is($_SERVER['HOME'], OS::getHome(), 'Could not get the user\'s home directory in UNIX.');
        unset($_SERVER);

        // Test for Windows.
        $expected             = 'X:\Users\ThisUser';
        $_SERVER['HOMEDRIVE'] = 'X:';
        $_SERVER['HOMEPATH']  = '\Users\ThisUser';
        is($expected, OS::getHome(), 'Could not get the user\'s home directory in Windows.');

        // In case the tests are not being run in isolation.
        $_SERVER = $oldServer;
    }

    public function testDirSize()
    {
        $dir = dirname(__FILE__) . '/dir1';

        @mkdir($dir);
        file_put_contents($dir . '/file1', '1234567890');
        file_put_contents($dir . '/file2', range('a', 'z'));

        is(10 + 26, FS::dirSize($dir));

        FS::rmdir($dir);
    }

    public function testLS()
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'dir1';

        @mkdir($dir);
        $file1 = $dir . DIRECTORY_SEPARATOR . 'file1';
        touch($file1);

        is(array($file1), FS::ls($dir));

        FS::rmdir($dir);
    }

    public function testFormat()
    {
        $size = FS::format(512, 0);
        is('512 B', $size);

        $size = FS::format(2048, 1);
        is('2.0 KB', $size);

        $size = FS::format(25151251, 2);
        is('23.99 MB', $size);

        $size = FS::format(19971597926, 2);
        is('18.60 GB', $size);

        $size = FS::format(2748779069440, 1);
        is('2.5 TB', $size);

        $size = FS::format(2.81475e15, 1);
        is('2.5 PB', $size);
    }

    public function testExt()
    {
        same('png', FS::ext('image.png'));
        same('png', FS::ext('image.jpg.png'));
        same('png', FS::ext('/file/path/image.jpg.png'));
        same('', FS::ext('image'));
        same('', FS::ext(''));
        same('', FS::ext(null));
        same('', FS::ext(false));
    }

    public function testBase()
    {
        same('image.png', FS::base('image.png'));
        same('image.jpg.png', FS::base('image.jpg.png'));
        same('image.jpg.png', FS::base('/file/path/image.jpg.png'));
        same('image', FS::base('image'));
        same('', FS::base(''));
        same('', FS::base(null));
        same('', FS::base(false));
    }

    public function testFilename()
    {
        same('image', FS::filename('image.png'));
        same('image.jpg', FS::filename('image.jpg.png'));
        same('image.jpg', FS::filename('/file/path/image.jpg.png'));
        same('image', FS::filename('image'));
        same('', FS::filename(''));
        same('', FS::filename(null));
        same('', FS::filename(false));
    }

    public function testDirname()
    {
        same('.', FS::dirname('image.png'));
        same('.', FS::dirname('image.jpg.png'));
        same('/file/path', FS::dirname('/file/path/image.jpg.png'));
        same('.', FS::dirname('image'));
        same('', FS::dirname(''));
        same('', FS::dirname(null));
        same('', FS::dirname(false));
    }

    public function testReal()
    {
        same(__FILE__, FS::real(__FILE__));
    }

    public function testClean()
    {
        $d     = DIRECTORY_SEPARATOR;
        $empty = Vars::get($_SERVER['DOCUMENT_ROOT'], '');

        same($empty, FS::clean(''));
        same($empty, FS::clean(false));
        same($empty, FS::clean(null));

        same('path', FS::clean('path'));
        same("{$d}path", FS::clean('/path'));
        same("{$d}path", FS::clean(' /path '));
        same("{$d}path{$d}", FS::clean('/path/'));
        same("{$d}path{$d}", FS::clean('///path///'));
        same("{$d}path{$d}path", FS::clean('///path///path'));
        same("{$d}path{$d}path{$d}path", FS::clean('///path///path/path'));
        same("{$d}path{$d}path{$d}path{$d}", FS::clean('\path\path\path\\\\\\\\'));
        same('\\path\\path\\path\\', FS::clean('\path\path\path\\\\\\\\', '\\'));
        same('\\path\\path\\path\\', FS::clean('\\path\\path\\path\\\\\\\\', '\\'));
        same('\\\\path\\path\\path\\', FS::clean('\\\\path\\path\\path\\\\\\\\', '\\'));
    }

    public function testStripExt()
    {
        same('image', FS::stripExt('image.png'));
        same('image.jpg', FS::stripExt('image.jpg.png'));
        same('/file/path/image.jpg', FS::stripExt('/file/path/image.jpg.png'));
    }


}
