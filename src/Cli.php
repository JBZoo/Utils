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

namespace JBZoo\Utils;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class Cli
{
    public const STDIN         = 0;
    public const STDOUT        = 1;
    public const STDERR        = 2;
    public const DEFAULT_WIDTH = 80;

    /**
     * Is command line mode.
     */
    public static function check(): bool
    {
        return \PHP_SAPI === 'cli' && \defined('STDOUT') && \defined('STDERR');
    }

    /**
     * Print line to std out.
     */
    public static function out(string $message, bool $addEol = true): bool
    {
        if ($addEol) {
            $message .= \PHP_EOL;
        }

        $stdout = \fopen('php://stdout', 'w');
        if ($stdout !== false && self::check()) {
            \fwrite($stdout, $message);

            return true;
        }

        /** @phan-suppress-next-line PhanPluginRemoveDebugEcho */
        echo $message;

        return false;
    }

    /**
     * Print line to std error.
     */
    public static function err(string $message, bool $addEol = true): bool
    {
        if ($addEol) {
            $message .= \PHP_EOL;
        }

        $stderr = \fopen('php://stderr', 'w');
        if ($stderr !== false && self::check()) {
            \fwrite($stderr, $message);

            return true;
        }

        /** @phan-suppress-next-line PhanPluginRemoveDebugEcho */
        echo $message;

        return false;
    }

    /**
     * Execute cli commands.
     */
    public static function exec(string $command, array $args = [], ?string $cwd = null, bool $verbose = false): string
    {
        if (!\class_exists(Process::class)) {
            throw new Exception('Symfony/Process package required for Cli::exec() method');
        }

        $realCommand = self::build($command, $args);

        if (!isStrEmpty($cwd)) {
            $realCwd = \realpath((string)$cwd);
            $cwd     = $realCwd === false ? null : $realCwd;
        } else {
            $cwd = null;
        }

        if ($verbose) {
            self::out("Process: {$realCommand}");
            self::out("CWD: {$cwd}");
        }

        try {
            // @phpstan-ignore-next-line
            if (\method_exists(Process::class, 'fromShellCommandline')) {
                $process = Process::fromShellCommandline($realCommand, $cwd, null, null, 3600);
            } else {
                $process = new Process([$realCommand], $cwd, null, null, 3600);
            }

            $process->run();
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage(), (int)$exception->getCode(), $exception);
        }

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Build params for cli options.
     */
    public static function build(string $command, array $args = []): string
    {
        $stringArgs  = [];
        $realCommand = $command;

        if (\count($args) > 0) {
            foreach ($args as $key => $value) {
                $value = \trim((string)$value);
                $key   = \trim((string)$key);

                if (!\str_starts_with($key, '-')) {
                    $key = \strlen($key) === 1 ? '-' . $key : '--' . $key;
                }

                if (!isStrEmpty($value)) {
                    $stringArgs[] = $key . '="' . \addcslashes($value, '"') . '"';
                } else {
                    $stringArgs[] = $key;
                }
            }
        }

        if (\count($stringArgs) > 0) {
            $realCommand = $command . ' ' . \implode(' ', $stringArgs);
        }

        return $realCommand;
    }

    /**
     * Returns true if STDOUT supports colorization.
     * This code has been copied and adapted from \Symfony\Component\Console\Output\OutputStream.
     */
    public static function hasColorSupport(): bool
    {
        if (!self::check()) {
            return false;
        }

        // @codeCoverageIgnoreStart
        if (Sys::isWin()) {
            return Env::bool('ANSICON') || Env::string('ConEmuANSI') === 'ON' || Env::string('TERM') === 'xterm';
        }
        // @codeCoverageIgnoreEnd

        return self::isInteractive(\STDOUT);
    }

    /**
     * Returns the number of columns of the terminal.
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function getNumberOfColumns(): int
    {
        // @codeCoverageIgnoreStart
        if (Sys::isWin()) {
            $columns = self::DEFAULT_WIDTH;

            if (\preg_match('/^(\d+)x\d+ \(\d+x(\d+)\)$/', Env::string('ANSICON'), $matches) > 0) {
                $columns = $matches[1];
            } elseif (\function_exists('proc_open')) {
                $process = \proc_open(
                    'mode CON',
                    [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
                    $pipes,
                    null,
                    null,
                    ['suppress_errors' => true],
                );

                if (\is_resource($process)) {
                    $info = (string)\stream_get_contents($pipes[1]);

                    \fclose($pipes[1]);
                    \fclose($pipes[2]);
                    \proc_close($process);

                    if (\preg_match('/--------+\r?\n.+?(\d+)\r?\n.+?(\d+)\r?\n/', $info, $matches) > 0) {
                        $columns = $matches[2];
                    }
                }
            }

            return (int)$columns - 1;
        }
        // @codeCoverageIgnoreEnd

        if (!self::isInteractive(self::STDIN)) {
            return self::DEFAULT_WIDTH;
        }

        /** @psalm-suppress ForbiddenCode */
        if ((\preg_match('#\d+ (\d+)#', (string)\shell_exec('stty size'), $match) === 1) && (int)$match[1] > 0) {
            return (int)$match[1];
        }

        /** @psalm-suppress ForbiddenCode */
        if ((\preg_match('#columns = (\d+);#', (string)\shell_exec('stty'), $match) === 1) && (int)$match[1] > 0) {
            return (int)$match[1];
        }

        return Env::int('COLUMNS', self::DEFAULT_WIDTH);
    }

    /**
     * Returns if the file descriptor is an interactive terminal or not.
     * @param int|resource $fileDescriptor
     */
    public static function isInteractive($fileDescriptor = self::STDOUT): bool
    {
        return \function_exists('posix_isatty') && @\posix_isatty($fileDescriptor);
    }
}
