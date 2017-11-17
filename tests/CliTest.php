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

use JBZoo\Utils\Cli;

/**
 * Class CliTest
 *
 * @package JBZoo\PHPUnit
 */
class CliTest extends PHPUnit
{

    public function testCheck()
    {
        isTrue(Cli::check());
    }

    public function testBuild()
    {
        is('ls', Cli::build('ls'));

        is('ls -a', Cli::build('ls', [
            'a' => '',
        ]));

        is('ls -a -l', Cli::build('ls', [
            'a' => '',
            'l' => '',
        ]));

        is('ls --help', Cli::build('ls', [
            'help' => '',
        ]));

        is('ls --option="qwerty"', Cli::build('ls', [
            'option' => 'qwerty',
        ]));

        is('ls --option="qwert\'y"', Cli::build('ls', [
            'option' => 'qwert\'y',
        ]));

        is('ls --option="qwert\"y"', Cli::build('ls', [
            'option' => 'qwert"y',
        ]));

        is('ls --option="0"', Cli::build('ls', [
            'option' => 0,
        ]));
    }

    public function testExec()
    {
        $output1 = Cli::exec('php -v');
        $output2 = Cli::exec('php', ['v' => '']);
        isSame($output1, $output2);
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function testExecFail()
    {
        Cli::exec('undefined-command');
    }

    /**
     * @covers \JBZoo\Utils\Cli::isInteractive
     */
    public function testCanDetectIfStdoutIsInteractiveByDefault()
    {
        $this->assertInternalType('boolean', Cli::isInteractive());
    }

    /**
     * @covers \JBZoo\Utils\Cli::isInteractive
     */
    public function testCanDetectIfFileDescriptorIsInteractive()
    {
        $this->assertInternalType('boolean', Cli::isInteractive(STDOUT));
    }

    /**
     * @covers \JBZoo\Utils\Cli::hasColorSupport
     * @uses   \JBZoo\Utils\Cli::isInteractive
     */
    public function testCanDetectColorSupport()
    {
        $this->assertInternalType('boolean', Cli::hasColorSupport());
    }

    /**
     * @covers \JBZoo\Utils\Cli::getNumberOfColumns
     * @uses   \JBZoo\Utils\Cli::isInteractive
     */
    public function testCanDetectNumberOfColumns()
    {
        $this->assertInternalType('integer', Cli::getNumberOfColumns());
    }
}
