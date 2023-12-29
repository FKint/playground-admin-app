<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('storage/framework')
    ->exclude('bootstrap/cache')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    ])
    ->setFinder($finder);
