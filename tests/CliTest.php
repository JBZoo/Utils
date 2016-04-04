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

use JBZoo\Utils\Cli;

/**
 * Class CliTest
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

        is('ls -a', Cli::build('ls', array(
            'a' => '',
        )));

        is('ls -a -l', Cli::build('ls', array(
            'a' => '',
            'l' => '',
        )));

        is('ls --help', Cli::build('ls', array(
            'help' => '',
        )));

        is('ls --option="qwerty"', Cli::build('ls', array(
            'option' => 'qwerty',
        )));

        is('ls --option="qwert\'y"', Cli::build('ls', array(
            'option' => 'qwert\'y',
        )));

        is('ls --option="qwert\"y"', Cli::build('ls', array(
            'option' => 'qwert"y',
        )));

        is('ls --option="0"', Cli::build('ls', array(
            'option' => 0,
        )));
    }

    public function testExec()
    {
        if (class_exists('\Symfony\Component\Process\Process')) {
            $output1 = Cli::exec('php -v');
            $output2 = Cli::exec('php', array('v' => ''));
            isSame($output1, $output2);
        } else {
            skip('Symfony/Process required to test Cli::exec() method');
        }
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
