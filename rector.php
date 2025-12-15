<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
    )
    ->withSkip([
        // validation
        RemoveUnusedConstructorParamRector::class => __DIR__ . '/src/Attributes',

        \Rector\Php55\Rector\String_\StringClassNameToClassConstantRector::class => __DIR__ . '/tests/Reflection/ClassNameResolver/ClassNameResolverTest.php',
    ])
    ->withSkip(['*/Fixture/*']);
