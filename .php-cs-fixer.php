<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

use Ergebnis\PhpCsFixer;

$header = <<<'TXT'
@package   mod_matrix
@copyright 2020, New Vector Ltd (Trading as Element)
@license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
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
