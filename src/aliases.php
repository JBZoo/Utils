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

function int(mixed $variable): int
{
    return Filter::int($variable);
}

function float(mixed $variable, int $round = 10): float
{
    return Filter::float($variable, $round);
}

function bool(mixed $variable): bool
{
    return Filter::bool($variable);
}

function slug(mixed $variable): string
{
    return Filter::alias($variable);
}

function digits(mixed $variable): string
{
    return Filter::digits($variable);
}

function alpha(mixed $variable): string
{
    return Filter::alpha($variable);
}

function alphanum(mixed $variable): string
{
    return Filter::alphanum($variable);
}

function isPath(mixed $variable): string
{
    return Filter::path($variable);
}

function cleanCmd(mixed $variable): string
{
    return Filter::cmd($variable);
}

function strip(mixed $variable): string
{
    return Filter::strip($variable);
}

function alias(mixed $variable): string
{
    return Filter::alias($variable);
}

function stripQuotes(mixed $variable): string
{
    return Filter::stripQuotes($variable);
}

function isStrEmpty(null|bool|string $variable): bool
{
    return Str::isEmpty($variable);
}
