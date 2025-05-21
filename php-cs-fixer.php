<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('tests/Fixtures')
    ->exclude('tests/cases');

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@DoctrineAnnotation' => true,
        '@Symfony:risky' => true,
        'declare_strict_types' => true,
        'function_to_constant' => true,
        'is_null' => true,
        'strict_param' => true,
        'concat_space' => ['spacing' => 'one'],
        'native_function_invocation' => [
            'scope' => 'all',
            'include' => ['@all'],
            'exclude' => [],
        ],
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ]);
