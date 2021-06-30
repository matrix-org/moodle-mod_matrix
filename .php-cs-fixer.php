<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

$config
    ->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php-cs-fixer.cache')
    ->setFinder($finder)
    ->setRules([]);

return $config;
