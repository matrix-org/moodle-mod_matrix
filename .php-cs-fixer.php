<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

use Ergebnis\PhpCsFixer;

$header = <<<'TXT'
@package   mod_matrix
@copyright 2020, New Vector Ltd (Trading as Element)
@license   https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
TXT;

$config = PhpCsFixer\Config\Factory::fromRuleSet(new PhpCsFixer\Config\RuleSet\Php73($header), [
    'final_class' => false,
    'phpdoc_no_package' => false,
    'psr_autoloading' => false,
]);

$config->getFinder()
    ->exclude([
        '.build/',
        '.data/',
        '.docker/',
        '.gitlab/',
        '.notes/',
    ])
    ->ignoreDotFiles(false)
    ->in(__DIR__)
    ->name('.php-cs-fixer.php');

$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php_cs.cache');

return $config;
