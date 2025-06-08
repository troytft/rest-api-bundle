<?php

declare(strict_types=1);

/**
 * @example vendor/bin/ecs check
 */
return \Symplify\EasyCodingStandard\Config\ECSConfig::configure()
    ->withCache(directory: '/tmp/php_ecs')
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/ecs.php',
    ])
    ->withParallel()
    ->withPreparedSets(psr12: true, common: true, symplify: true, strict: true, cleanCode: true)
    ->withPhpCsFixerSets(doctrineAnnotation: true, symfony: true, symfonyRisky: true)
    ->withSkip([
        // Need to think about this
        \PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer::class,
        \PhpCsFixer\Fixer\Comment\SingleLineCommentSpacingFixer::class,
        \PhpCsFixer\Fixer\Whitespace\TypesSpacesFixer::class,
        \PhpCsFixer\Fixer\Operator\IncrementStyleFixer::class,
        \PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer::class,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer::class,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitInternalClassFixer::class,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer::class,
        \PhpCsFixer\Fixer\StringNotation\EscapeImplicitBackslashesFixer::class,
        \PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer::class,
        \PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer::class,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer::class,
        \Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer::class, // Maybe if we ignore controllers
        \Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer::class, // Maybe if we ignore controllers
        \Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer::class, // Maybe if we ignore controllers
        \Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer::class,
        \PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer::class,
        \PhpCsFixer\Fixer\LanguageConstruct\ErrorSuppressionFixer::class,

        // Does not align with project requirements
        \PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer::class,
        \PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer::class,
        \PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer::class,
        \PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer::class,
        \PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer::class,
        \PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer::class,
        \PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer::class,
        \PhpCsFixer\Fixer\Operator\NewWithParenthesesFixer::class,
    ])
    ->withConfiguredRule(\PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer::class, ['on_multiline' => 'ignore'])
    ->withConfiguredRule(\PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer::class, ['equal' => false, 'identical' => false, 'less_and_greater' => null])
    ->withConfiguredRule(\PhpCsFixer\Fixer\Operator\ConcatSpaceFixer::class, ['spacing' => 'one'])
    ->withConfiguredRule(\PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer::class, ['align' => 'left'])
    ->withConfiguredRule(\PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer::class, ['operator' => ':'])
    ->withConfiguredRule(\PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer::class, ['before_array_assignments_colon' => false])
    ->withConfiguredRule(\PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class, ['elements' => ['property' => 'one', 'method' => 'one']])
    ->withConfiguredRule(\PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff::class, [
        'forbiddenFunctions' => [
            'var_dump' => null,
            'eval' => null,
            'sizeof' => 'count',
        ],
    ])
    ->withConfiguredRule(\PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer::class,
        [
            'include' => ['@all'],
            'scope' => 'all',
            'strict' => false,
        ])
    ->withRules([
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\DoubleQuoteUsageSniff::class,
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\DisallowMultipleAssignmentsSniff::class,
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope\StaticThisUsageSniff::class,
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\LowercasePHPFunctionsSniff::class,
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\GlobalKeywordSniff::class,
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\Operators\ValidLogicalOperatorsSniff::class,
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ClassFileNameSniff::class,
    ]);
