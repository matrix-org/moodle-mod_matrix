<?php

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
    ->setRules([]);

return $config;
