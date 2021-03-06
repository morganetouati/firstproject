<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/config')
    ->in(__DIR__.'/public')
    ->in(__DIR__.'/src');

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules(
        [
            '@DoctrineAnnotation' => true,
            '@PHP71Migration' => true,
            '@PHP71Migration:risky' => true,
            '@Symfony' => true,
            '@Symfony:risky' => true,
            'array_syntax' => ['syntax' => 'short'],
            'concat_space' => ['spacing' => 'one'],
            'native_function_invocation' => true,
            'ordered_imports' => true,
            'phpdoc_order' => true,
            'strict_comparison' => true,
            'strict_param' => true,
        ]
    )
    ->setUsingCache(false);