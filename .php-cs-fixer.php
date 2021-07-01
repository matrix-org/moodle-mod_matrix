<?php

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$header = <<<'TXT'
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
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'default',
                'do',
                'exit',
                'for',
                'foreach',
                'goto',
                'if',
                'include',
                'include_once',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'while',
                'yield',
            ],
        ],
        'braces' => [
            'allow_single_line_anonymous_class_with_empty_body' => true,
            'allow_single_line_closure' => false,
            'position_after_anonymous_constructs' => 'same',
            'position_after_control_structures' => 'same',
            'position_after_functions_and_oop_constructs' => 'next',
        ],
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
            ],
        ],
        'concat_space' => [
            'spacing' => 'one',
        ],
        'header_comment' => [
            'comment_type' => 'PHPDoc',
            'header' => $header,
            'location' => 'after_declare_strict',
            'separate' => 'both',
        ],
        'heredoc_to_nowdoc' => true,
        'include' => true,
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'method_argument_space' => [
            'after_heredoc' => false,
            'keep_multiple_spaces_after_comma' => false,
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'no_alias_functions' => [
            'sets' => [
                '@IMAP',
                '@internal',
            ],
        ],
        'no_alias_language_construct_call' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'break',
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
                'use',
                'use_trait',
            ],
        ],
        'no_superfluous_elseif' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unneeded_control_parentheses' => [
            'statements' => [
                'break',
                'clone',
                'continue',
                'echo_print',
                'return',
                'switch_case',
                'yield',
            ],
        ],
        'no_unused_imports' => true,
    ]);

return $config;
