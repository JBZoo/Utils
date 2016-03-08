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

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class Cli
 * @package JBZoo\Utils
 */
class Cli
{
    /**
     * Is command line
     *
     * @return bool
     */
    public static function check()
    {
        return PHP_SAPI === 'cli' || defined('STDOUT');
    }

    /**
     * Print line to std out
     *
     * @param string $message
     * @param bool   $addEol
     * @codeCoverageIgnore
     */
    public static function out($message, $addEol = true)
    {
        if ($addEol) {
            $message .= PHP_EOL;
        }

        if (defined('STDOUT')) {
            fwrite(STDOUT, $message);
        } else {
            echo $message;
        }
    }

    /**
     * Print line to std error
     *
     * @param string $message
     * @param bool   $addEol
     * @codeCoverageIgnore
     */
    public static function err($message, $addEol = true)
    {
        if ($addEol) {
            $message .= PHP_EOL;
        }

        if (defined('STDERR')) {
            fwrite(STDERR, $message);
        } else {
            echo $message;
        }
    }

    /**
     * Execute cli commands
     *
     * @param string $command
     * @param array  $args
     * @param null   $cwd
     * @param bool   $verbose
     * @return string
     * @throws ProcessFailedException
     * @throws \Exception
     */
    public static function exec($command, $args = array(), $cwd = null, $verbose = false)
    {
        if (!class_exists('\Symfony\Component\Process\Process')) {
            throw new \Exception("Symfony/Process package required for Cli::exec() method");
        }

        $cmd = self::build($command, $args);
        $cwd = $cwd ? $cwd = realpath($cwd) : null;

        //@codeCoverageIgnoreStart
        if ($verbose) {

            // Only in testing mode
            if (function_exists('\JBZoo\PHPUnit\cliMessage')) {
                \JBZoo\PHPUnit\cliMessage('Process: ' . $cmd);
                \JBZoo\PHPUnit\cliMessage('CWD: ' . $cwd);
            } else {
                Cli::out('Process: ' . $cmd);
                Cli::out('CWD: ' . $cwd);
            }

        }
        //@codeCoverageIgnoreEnd

        // execute command
        $process = new Process($cmd, $cwd);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Build params for cli options
     *
     * @param string $command
     * @param array  $args
     * @return string
     */
    public static function build($command, $args = array())
    {
        $stringArgs  = array();
        $realCommand = $command;

        if (count($args) > 0) {

            foreach ($args as $key => $value) {
                $value = trim($value);
                $key   = trim($key);

                if (strpos($key, '-') !== 0) {
                    if (strlen($key) == 1) {
                        $key = '-' . $key;
                    } else {
                        $key = '--' . $key;
                    }
                }

                if ($value) {
                    $stringArgs[] = $key . '="' . addcslashes($value, '"') . '"';
                } else {
                    $stringArgs[] = $key;
                }
            }
        }

        if (count($stringArgs)) {
            $realCommand = $command . ' ' . implode(' ', $stringArgs);
        }

        return $realCommand;
    }
}
