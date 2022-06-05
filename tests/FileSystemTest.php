<?php

/**
 * JBZoo Toolbox - Utils
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Utils
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Utils
 */

declare(strict_types=1);

namespace JBZoo\PHPUnit;

use JBZoo\Utils\FS;
use JBZoo\Utils\Sys;

/**
 * Class FileSystemTest
 *
 * @package JBZoo\PHPUnit
 */
class FileSystemTest extends PHPUnit
{
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER['DOCUMENT_ROOT'] = PROJECT_ROOT;
    }

    public function testRemoveDir(): void
    {
        $dirname = __DIR__;

        // Test deleting a non-existing directory
        isFalse(file_exists($dirname . '/test1'));
        isTrue(FS::rmDir($dirname . '/test1'));

        // Test deleting an empty directory
        $dir = $dirname . '/test2';
        mkdir($dir);

        isTrue(is_dir($dir));

        if (is_dir($dir)) {
            FS::rmDir($dir);
            isFalse(is_dir($dir));
        }

        // Test deleting a non-empty directory
        $dir = $dirname . '/test3';
        $file = $dirname . '/test3/test.txt';
        mkdir($dir);
        touch($file);

        isTrue(is_dir($dir));
        isTrue(is_file($file));

        if (is_dir($dir)) {
            FS::rmDir($dir);
            isFalse(is_dir($dir));
            isFalse(is_file($file));
        }

        // Test deleting a non-directory path
        $file = $dirname . '/test4.txt';
        touch($file);

        try {
            FS::rmDir($file);
            isTrue(false);
        } catch (\Exception $e) {
            isTrue(true);
        }

        unlink($file);

        // Test deleting a nested directory
        $dir1 = $dirname . '/test5';
        $dir2 = $dirname . '/test5/nested_dir';
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
            FS::rmDir($dir1);
            isFalse(is_dir($dir1));
            isFalse(is_dir($dir2));
            isFalse(is_file($file1));
            isFalse(is_file($file2));
        }

        // Test symlink traversal.
        $dir = $dirname . '/test6';
        $nestedDir = "$dir/nested";
        $symlink = "$dir/nested-symlink";
        @mkdir($dir);
        @mkdir($nestedDir);

        $symlinkStatus = symlink($nestedDir, $symlink);
        isTrue($symlinkStatus, 'The test system does not support making symlinks.');

        if (!$symlink) {
            return;
        }

        isTrue(FS::rmDir($symlink, true), 'Could not delete a symlinked directory.');
        isFalse(file_exists($symlink), 'Could not delete a symlinked directory.');

        FS::rmDir($dir, true);
        isFalse(is_dir($dir), 'Could not delete a directory with a symlinked directory inside of it.');
    }

    public function testOpenFile(): void
    {
        isContain('FS::openFile(', FS::openFile(__FILE__));
    }

    public function testFirstLine(): void
    {
        isContain('<?php', FS::firstLine(__FILE__));
        isNull(FS::firstLine(__FILE__ . '_noexists'));
    }

    public function testPerms(): void
    {
        isFalse((bool)FS::perms('/no/such/file'));
    }

    public function testWritable(): void
    {
        if (Sys::isWin()) {
            //skip('This functionality is not working on Windows.');
            return;
        }

        if (Sys::isRoot()) {
            skip('These tests don\'t work when run as root');
        }

        isFalse(FS::writable('/no/such/file'));

        // Create a file to test with
        $dirname = __DIR__;
        $file = $dirname . '/test7';
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
        /** @noinspection ArgumentEqualsDefaultValueInspection */
        FS::writable($file, true);
        clearstatcache();
        isTrue(is_writable($file));
        is('-rw-r--r--', FS::perms($file));

        unlink($file);
    }

    public function testReadable(): void
    {
        if (Sys::isWin()) {
            //skip('This functionality is not working on Windows.');
            return;
        }

        if (Sys::isRoot()) {
            skip('These tests don\'t work when run as root');
        }

        isFalse(FS::readable('/no/such/file'));

        $dirName = __DIR__;
        $file = $dirName . '/test8';
        touch($file);

        isTrue(is_readable($file));

        FS::readable($file, false);
        clearstatcache();
        isFalse(is_readable($file));

        /** @noinspection ArgumentEqualsDefaultValueInspection */
        FS::readable($file, true);
        clearstatcache();
        isTrue(is_readable($file));

        unlink($file);
    }

    public function testExecutable(): void
    {
        isFalse(FS::executable('/no/such/file'));

        $dirname = __DIR__;
        $file = $dirname . '/test9';
        touch($file);

        isFalse(is_executable($file));

        /** @noinspection ArgumentEqualsDefaultValueInspection */
        FS::executable($file, true);
        clearstatcache();
        isTrue(is_executable($file));

        FS::executable($file, false);
        clearstatcache();
        isFalse(is_executable($file));

        unlink($file);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testGetHome(): void
    {
        isTrue(is_writable(Sys::getHome()));
        isNotEmpty(Sys::getHome());

        //skip('Should be redesigned');
        //// Test for OS Default.
        //
        //$oldServer = $_SERVER;
        //unset($_SERVER);
        //
        //// Test for UNIX.
        //$_SERVER['HOME'] = '/home/unknown';
        ////is($_SERVER['HOME'], Sys::getHome(), "Could not get the user's home directory in UNIX.");
        //unset($_SERVER);
        //
        //// Test for Windows.
        //$expected = 'X:\Users\ThisUser';
        //$_SERVER['HOMEDRIVE'] = 'X:';
        //$_SERVER['HOMEPATH'] = '\Users\ThisUser';
        ////is($expected, Sys::getHome(), "Could not get the user's home directory in Windows.");
        //
        //// In case the tests are not being run in isolation.
        //$_SERVER = $oldServer;
    }

    public function testDirSize(): void
    {
        $dir = __DIR__ . '/dir1';

        @mkdir($dir);
        file_put_contents($dir . '/file1', '1234567890');
        file_put_contents($dir . '/file2', range('a', 'z'));

        is(10 + 26, FS::dirSize($dir));

        FS::rmDir($dir);
    }

    public function testLS(): void
    {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . 'dir1';

        @mkdir($dir);
        $file1 = $dir . DIRECTORY_SEPARATOR . 'file1.txt';
        touch($file1);

        is([$file1], FS::ls($dir));

        FS::rmDir($dir);
    }

    public function testFormat(): void
    {
        $size = FS::format(512, 0);
        is('512 B', $size);

        $size = FS::format(512);
        is('512 B', $size);

        $size = FS::format(2048, 1);
        is('2.0 KB', $size);

        $size = FS::format(25151251);
        is('23.99 MB', $size);

        $size = FS::format(19971597926);
        is('18.60 GB', $size);

        $size = FS::format(2748779069440, 1);
        is('2.5 TB', $size);

        $size = FS::format((int)2.81475e15, 1);
        is('2.5 PB', $size);
    }

    public function testExt(): void
    {
        isSame('png', FS::ext('image.png'));
        isSame('png', FS::ext('image.jpg.png'));
        isSame('png', FS::ext('/file/path/image.jpg.png'));
        isSame('', FS::ext('image'));
        isSame('', FS::ext(''));
        isSame('', FS::ext(null));

        // URl
        isSame('txt', FS::ext('file.txt?some_var=123456'));
        isSame('txt', FS::ext('file.txt?some_var=123456?invalid=param'));
        isSame(
            'php',
            FS::ext('http://demo.jbzoo.com/sites/phones/smartfony.php?'
                . 'logic=and&exact=0&controller=search&option=com_zoo&task=filter&type=phone&app_id=1&Itemid=101')
        );
        isSame(
            '',
            FS::ext('http://demo.jbzoo.com/sites/phones/smartfony?'
                . 'logic=and&exact=0&controller=search&option=com_zoo&task=filter&type=phone&app_id=1&Itemid=101')
        );

        // to lower
        isSame('png', FS::ext('image.PNG'));
        isSame('png', FS::ext('image.PnG'));
    }

    public function testBase(): void
    {
        isSame('image.png', FS::base('image.png'));
        isSame('image.jpg.png', FS::base('image.jpg.png'));
        isSame('image.jpg.png', FS::base('/file/path/image.jpg.png'));
        isSame('image', FS::base('image'));
        isSame('', FS::base(''));
        isSame('', FS::base(null));
    }

    public function testFilename(): void
    {
        isSame('image', FS::filename('image.png'));
        isSame('image.jpg', FS::filename('image.jpg.png'));
        isSame('image.jpg', FS::filename('/file/path/image.jpg.png'));
        isSame('image', FS::filename('image'));
        isSame('', FS::filename(''));
        isSame('', FS::filename(null));
    }

    public function testDirname(): void
    {
        isSame('.', FS::dirName('image.png'));
        isSame('.', FS::dirName('image.jpg.png'));
        isSame('/file/path', FS::dirName('/file/path/image.jpg.png'));
        isSame('.', FS::dirName('image'));
        isSame('', FS::dirName(''));
        isSame('', FS::dirName(null));
    }

    public function testReal(): void
    {
        isSame(__FILE__, FS::real(__FILE__));
    }

    public function testClean(): void
    {
        isSame('', FS::clean(''));
        isSame('', FS::clean(null));

        isSame('path', FS::clean('path'));
        isSame('/path', FS::clean('/path'));
        isSame('/path', FS::clean(' /path '));
        isSame('/path/', FS::clean('/path/'));
        isSame('/path/', FS::clean('///path///'));
        isSame('/path/path', FS::clean('///path///path'));
        isSame('/path/path/path', FS::clean('///path///path/path'));
        isSame('/path/path/path/', FS::clean('\path\path\path\\\\\\\\'));
        isSame('\\path\\path\\path\\', FS::clean('\path\path\path\\\\\\\\', '\\'));
        isSame('\\path\\path\\path\\', FS::clean('\\path\\path\\path\\\\\\\\', '\\'));
        isSame('\\\\path\\path\\path\\', FS::clean('\\\\path\\path\\path\\\\\\\\', '\\'));

        isSame('../../path/', FS::clean('..///..///path/', '/'));
        isSame('./../path/', FS::clean('.///..///path/', '/'));
        isSame('/../../path/', FS::clean('/..///..///path/', '/'));
    }

    public function testStripExt(): void
    {
        isSame('image', FS::stripExt('image.png'));
        isSame('image.jpg', FS::stripExt('image.jpg.png'));
        isSame('/file/path/image.jpg', FS::stripExt('/file/path/image.jpg.png'));
    }

    public function testIsDir(): void
    {
        isFalse(FS::isDir(__FILE__));
        isTrue(FS::isDir(__DIR__));
    }

    public function testIsFile(): void
    {
        isFalse(FS::isFile(__DIR__));
        isTrue(FS::isFile(__FILE__));
    }

    public function testIsReal(): void
    {
        isTrue(FS::isReal(__FILE__));
        isFalse(FS::isReal(__DIR__ . '/../'));
    }

    public function testGetRelative(): void
    {
        $file = __FILE__;

        $root = __DIR__;
        isSame('FileSystemTest.php', FS::getRelative($file, $root, '/'));
        isSame('FileSystemTest.php', FS::getRelative($file, $root, '\\'));

        $root = __DIR__ . '/..';
        isSame('tests/FileSystemTest.php', FS::getRelative($file, $root, '/'));
        isSame('tests\\FileSystemTest.php', FS::getRelative($file, $root, '\\'));

        $root = null;
        isSame('tests/FileSystemTest.php', FS::getRelative($file, $root, '/'));
        isSame('tests\\FileSystemTest.php', FS::getRelative($file, $root, '\\'));
    }
}
