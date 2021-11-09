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
    'blank_line_before_statement' => false,
    'declare_strict_types' => false,
    'final_class' => false,
    'native_function_invocation' => false,
    'phpdoc_no_package' => false,
    'phpdoc_separation' => false,
    'psr_autoloading' => false,
    'simple_to_complex_string_variable' => false,
    'single_line_after_imports' => false,
    'strict_comparison' => false,
    'trailing_comma_in_multiline' => false,
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
