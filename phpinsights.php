<?php

declare(strict_types=1);

return [
    'preset' => 'default',
    'exclude' => [
        'Kernel.php',
        'var',
        'translations',
        'config',
        'public',
        'src/Migrations', // will exclude Migrations Folder in src
        '*Repository.php', // will exclude every php files that match pattern
        'src/Kernel.php' // will exclude this file only
    ],
    'add' => [
        //  ExampleMetric::class => [
        //      ExampleInsight::class,
        //  ]
    ],
    'remove' => [
        //  ExampleInsight::class,
        SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class,
        NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class,
        SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff::class,
        PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff::class

    ],
    'config' => [
        //  ExampleInsight::class => [
        //      'key' => 'value',
        //  ],
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 160
        ],
        \SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class => [
            'maxLinesLength' => 30,
        ],
        \NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class => [
            'maxComplexity' => 20,
        ],
        \SlevomatCodingStandard\Sniffs\Namespaces\UseSpacingSniff::class => [
            'linesCountBeforeFirstUse' => 1,
            'linesCountBetweenUseTypes' => 1,
            'linesCountAfterLastUse' => 1,
        ]



    ],
    'ide' => 'phpstorm'
];
