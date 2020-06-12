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
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\Utils;

/**
 * @param mixed $variable
 * @return int
 */
function int($variable): int
{
    return Filter::int($variable);
}

/**
 * @param mixed $variable
 * @param int   $round
 * @return float
 */
function float($variable, int $round = 10): float
{
    return Filter::float($variable, $round);
}

/**
 * @param mixed $variable
 * @return bool
 */
function bool($variable): bool
{
    return Filter::bool($variable);
}

/**
 * @param mixed $variable
 * @return string
 */
function slug($variable): string
{
    return Filter::alias($variable);
}

/**
 * @param mixed $variable
 * @return string
 */
function digits($variable): string
{
    return Filter::digits($variable);
}

/**
 * @param mixed $variable
 * @return string
 */
function alpha($variable): string
{
    return Filter::alpha($variable);
}

/**
 * @param mixed $variable
 * @return string
 */
function alphanum($variable): string
{
    return Filter::alphanum($variable);
}

/**
 * @param mixed $variable
 * @return string
 */
function isPath($variable): string
{
    return Filter::path($variable);
}

/**
 * @param mixed $variable
 * @return string
 */
function cleanCmd($variable): string
{
    return Filter::cmd($variable);
}

/**
 * @param mixed $variable
 * @return string
 */
function strip($variable): string
{
    return Filter::strip($variable);
}

/**
 * @param mixed $variable
 * @return string
 */
function alias($variable): string
{
    return Filter::alias($variable);
}

/**
 * @param mixed $variable
 * @return string
 */
function stripQuotes($variable): string
{
    return Filter::stripQuotes($variable);
}
