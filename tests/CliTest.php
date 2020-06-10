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

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Cli;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
        is('ls -0="qwerty"', Cli::build('ls', ['qwerty']));
        is('ls -a', Cli::build('ls', ['a' => null]));
        is('ls -a', Cli::build('ls', ['a' => '']));
        is('ls -a', Cli::build('ls', ['a' => false]));
        is('ls -a="1"', Cli::build('ls', ['a' => true]));

        is('ls -a -l', Cli::build('ls', [
            'a' => '',
            'l' => '',
        ]));

        is('ls --help', Cli::build('ls', ['help' => '']));
        is('ls --option="qwerty"', Cli::build('ls', ['option' => 'qwerty']));
        is('ls --option="qwert\'y"', Cli::build('ls', ['option' => 'qwert\'y']));
        is('ls --option="qwert\"y"', Cli::build('ls', ['option' => 'qwert"y']));
        is('ls --option', Cli::build('ls', ['option' => 0]));
    }

    public function testExec()
    {
        $output1 = Cli::exec('php -v');
        $output2 = Cli::exec('php', ['v' => '']);
        isSame($output1, $output2);
    }

    public function testExecFail()
    {
        $this->expectException(ProcessFailedException::class);

        Cli::exec('undefined-command');
    }

    public function testCanDetectIfStdoutIsInteractiveByDefault()
    {
        isTrue(Cli::isInteractive());
    }

    public function testCanDetectIfFileDescriptorIsInteractive()
    {
        $this->assertIsBool(Cli::isInteractive(STDOUT));
    }

    public function testCanDetectColorSupport()
    {
        isTrue(Cli::hasColorSupport());
    }

    public function testCanDetectNumberOfColumns()
    {
        $this->assertIsInt(Cli::getNumberOfColumns());
    }

    public function testStdMessage()
    {
        // Just no errors
        isTrue(Cli::out('message1'));
        isTrue(Cli::out('message2', true));
        success();
    }

    public function testErrorMessage()
    {
        // Just no errors
        isTrue(Cli::err('error1'));
        isTrue(Cli::err('error2', true));
        success();
    }

    public function testGetNumberOfColumns()
    {
        isTrue(Cli::getNumberOfColumns() >= 80);
    }
}
