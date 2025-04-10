<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->in(__DIR__ . '/bin')
    ->in(__DIR__ . '/bootstrap')
    ->in(__DIR__ . '/config')
    ->in(__DIR__ . '/public')
    ->name('*.php')
    ->exclude('vendor')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,

        'strict_param' => true,                      // require strict types
        'declare_strict_types' => true,              // ensure declare(strict_types=1)
        'array_syntax' => ['syntax' => 'short'],     // use [] instead of array()
        'ordered_imports' => ['sort_algorithm' => 'alpha'], // sort use statements
        'no_unused_imports' => true,                 // remove unused use statements
        'single_quote' => true,                      // enforce single quotes when possible
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'no_extra_blank_lines' => [
            'tokens' => ['extra', 'use', 'return', 'throw'],
        ],
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',                   // 1 blank line between methods
            ],
        ],
    ])
    ->setFinder($finder);
