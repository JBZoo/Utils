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
    }

    public function testExec()
    {
        $output1 = Cli::exec('php -v');
        $output2 = Cli::exec('php', array('v' => ''));
        isSame($output1, $output2);
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function testExecFail()
    {
        Cli::exec('undefined-command');
    }
}
