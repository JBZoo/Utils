<?php

/**
 * JBZoo Toolbox - Utils.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Utils
 */

declare(strict_types=1);

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Cli;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CliTest extends PHPUnit
{
    public function testCheck(): void
    {
        isTrue(Cli::check());
    }

    public function testBuild(): void
    {
        is('ls', Cli::build('ls'));
        is('ls -0="qwerty"', Cli::build('ls', ['qwerty']));
        is('ls -a', Cli::build('ls', ['a' => null]));
        is('ls -a', Cli::build('ls', ['a' => '']));
        is('ls -a', Cli::build('ls', ['a' => false]));
        is('ls -a="1"', Cli::build('ls', ['a' => true]));

        is('ls -a -l', Cli::build('ls', ['a' => '', 'l' => '']));

        is('ls --help', Cli::build('ls', ['help' => '']));
        is('ls --option="qwerty"', Cli::build('ls', ['option' => 'qwerty']));
        is('ls --option="qwert\'y"', Cli::build('ls', ['option' => 'qwert\'y']));
        is('ls --option="qwert\"y"', Cli::build('ls', ['option' => 'qwert"y']));
        is('ls --option="0"', Cli::build('ls', ['option' => 0]));
    }

    public function testExec(): void
    {
        $output1 = Cli::exec('php -v');
        $output2 = Cli::exec('php', ['v' => '']);
        isSame($output1, $output2);
    }

    public function testExecFail(): void
    {
        $this->expectException(ProcessFailedException::class);

        Cli::exec('undefined-command');
    }

    public function testCanDetectColorSupport(): void
    {
        skip('it needs a new idea to test it');
        isTrue(Cli::hasColorSupport());
    }

    public function testCanDetectNumberOfColumns(): void
    {
        self::assertIsInt(Cli::getNumberOfColumns());
    }

    public function testStdMessage(): void
    {
        // Just no errors
        isTrue(Cli::out('message1'));
        isTrue(Cli::out('message2', true));
        success();
    }

    public function testErrorMessage(): void
    {
        // Just no errors
        isTrue(Cli::err('error1'));
        isTrue(Cli::err('error2', true));
        success();
    }

    public function testGetNumberOfColumns(): void
    {
        isTrue(Cli::getNumberOfColumns() >= 80);
    }
}
