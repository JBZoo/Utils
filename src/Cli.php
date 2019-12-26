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

namespace JBZoo\Utils;

use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use function JBZoo\PHPUnit\cliMessage;

/**
 * Class Cli
 *
 * @package JBZoo\Utils
 */
class Cli
{
    public const STDIN  = 0;
    public const STDOUT = 1;
    public const STDERR = 2;

    /**
     * Is command line
     *
     * @return bool
     */
    public static function check(): bool
    {
        return PHP_SAPI === 'cli' && defined('STDOUT');
    }

    /**
     * Print line to std out
     *
     * @param string $message
     * @param bool   $addEol
     * @codeCoverageIgnore
     */
    public static function out($message, $addEol = true): void
    {
        if ($addEol) {
            $message .= PHP_EOL;
        }

        if (self::check()) {
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
    public static function err($message, $addEol = true): void
    {
        if ($addEol) {
            $message .= PHP_EOL;
        }

        if (self::check()) {
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
     * @throws RuntimeException
     * @throws Exception
     */
    public static function exec($command, array $args = [], $cwd = null, $verbose = false): string
    {
        if (!class_exists(Process::class)) {
            throw new Exception('Symfony/Process package required for Cli::exec() method'); // @codeCoverageIgnore
        }

        $cmd = self::build($command, $args);
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $cwd = $cwd ? realpath($cwd) : null;

        //@codeCoverageIgnoreStart
        if ($verbose) {
            // Only in testing mode
            if (function_exists('\JBZoo\PHPUnit\cliMessage')) {
                cliMessage('Process: ' . $cmd);
                cliMessage('CWD: ' . $cwd);
            } else {
                self::out('Process: ' . $cmd);
                self::out('CWD: ' . $cwd);
            }
        }
        //@codeCoverageIgnoreEnd

        // execute command
        try {
            if (method_exists(Process::class, 'fromShellCommandline')) {
                $process = Process::fromShellCommandline($cmd, $cwd, null, null, 3600);
            } else {
                $process = new Process([$cmd], $cwd, null, null, 3600);
            }

            $process->run();
        } catch (\Exception $exception) {
            throw new Exception($exception, (int)$exception->getCode(), $exception);
        }

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
    public static function build($command, array $args = []): string
    {
        $stringArgs = [];
        $realCommand = $command;

        if (count($args) > 0) {
            foreach ($args as $key => $value) {
                $value = trim($value);
                $key = trim($key);

                if (strpos($key, '-') !== 0) {
                    $key = strlen($key) === 1 ? '-' . $key : '--' . $key;
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

    /**
     * Returns true if STDOUT supports colorization.
     *
     * This code has been copied and adapted from
     * Symfony\Component\Console\Output\OutputStream.
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public static function hasColorSupport(): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return Env::get('ANSICON', Env::VAR_BOOL)
                || 'ON' === Env::get('ConEmuANSI')
                || 'xterm' === Env::get('TERM');
        }

        if (!self::check()) {
            return false;
        }

        return self::isInteractive(STDOUT);
    }

    /**
     * Returns the number of columns of the terminal.
     *
     * @return int
     * @codeCoverageIgnore
     */
    public static function getNumberOfColumns(): int
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $columns = 80;

            if (preg_match('/^(\d+)x\d+ \(\d+x(\d+)\)$/', trim(getenv('ANSICON')), $matches)) {
                $columns = $matches[1];
            } elseif (function_exists('proc_open')) {
                $process = proc_open(
                    'mode CON',
                    [
                        1 => ['pipe', 'w'],
                        2 => ['pipe', 'w'],
                    ],
                    $pipes,
                    null,
                    null,
                    ['suppress_errors' => true]
                );

                if (is_resource($process)) {
                    $info = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_close($process);
                    if (preg_match('/--------+\r?\n.+?(\d+)\r?\n.+?(\d+)\r?\n/', $info, $matches)) {
                        $columns = $matches[2];
                    }
                }
            }

            return $columns - 1;
        }

        if (!self::isInteractive(self::STDIN)) {
            return 80;
        }

        if ((preg_match('#\d+ (\d+)#', shell_exec('stty size'), $match) === 1) && (int)$match[1] > 0) {
            return (int)$match[1];
        }

        if ((preg_match('#columns = (\d+);#', shell_exec('stty'), $match) === 1) && (int)$match[1] > 0) {
            return (int)$match[1];
        }

        return 80;
    }

    /**
     * Returns if the file descriptor is an interactive terminal or not.
     *
     * @param int|resource $fileDescriptor
     * @return bool
     */
    public static function isInteractive($fileDescriptor = self::STDOUT): bool
    {
        return function_exists('posix_isatty') && @posix_isatty($fileDescriptor);
    }
}
