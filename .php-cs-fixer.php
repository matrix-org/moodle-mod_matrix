<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$header = <<<TXT
@package   mod_matrix
@copyright 2020, New Vector Ltd (Trading as Element)
@license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
TXT;

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->exclude([
        '.build/',
        '.gitlab/',
    ])
    ->in(__DIR__)
    ->name('.php-cs-fixer.php');

$config = new PhpCsFixer\Config();

$config
    ->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php-cs-fixer.cache')
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        'array_push' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'header_comment' => [
            'comment_type' => 'PHPDoc',
            'header' => $header,
            'location' => 'after_declare_strict',
            'separate' => 'both',
        ],
    ]);

return $config;
