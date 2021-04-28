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

declare(strict_types=1);

$default = include __DIR__ . '/vendor/jbzoo/codestyle/src/phan/default.php';

$config = array_merge($default, [
    'directory_list' => [
        'src',

        'vendor/symfony/process',
        'vendor/jbzoo/data',
    ]
]);

$configIndex = array_search('UnusedSuppressionPlugin', $config['plugins'], true);
if ($configIndex !== false) {
    unset($config['plugins'][$configIndex]);
}

return $config;
