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
        is('ls -a', Cli::build('ls', ['a' => '']));

        is('ls -a -l', Cli::build('ls', [
            'a' => '',
            'l' => '',
        ]));

        is('ls --help', Cli::build('ls', ['help' => '']));
        is('ls --option="qwerty"', Cli::build('ls', ['option' => 'qwerty']));
        is('ls --option="qwert\'y"', Cli::build('ls', ['option' => 'qwert\'y']));
        is('ls --option="qwert\"y"', Cli::build('ls', ['option' => 'qwert"y']));
        is('ls --option', Cli::build('ls', ['option' => 0,]));
    }

    public function testExec()
    {
        $output1 = Cli::exec('php -v');
        $output2 = Cli::exec('php', ['v' => '']);
        isSame($output1, $output2);
    }

    public function testExecFail()
    {
        $this->expectException(\Symfony\Component\Process\Exception\ProcessFailedException::class);

        Cli::exec('undefined-command');
    }

    public function testCanDetectIfStdoutIsInteractiveByDefault()
    {
        $this->assertIsBool(Cli::isInteractive());
    }

    public function testCanDetectIfFileDescriptorIsInteractive()
    {
        $this->assertIsBool(Cli::isInteractive(STDOUT));
    }

    public function testCanDetectColorSupport()
    {
        $this->assertIsBool(Cli::hasColorSupport());
    }

    public function testCanDetectNumberOfColumns()
    {
        $this->assertIsInt(Cli::getNumberOfColumns());
    }
}
