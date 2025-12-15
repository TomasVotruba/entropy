<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;

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
        // false-positive validation
        RemoveUnusedConstructorParamRector::class => __DIR__ . '/src/Attributes',
        RemoveEmptyClassMethodRector::class => __DIR__ . '/src/Attributes',

        // testing string to class name resolution
        StringClassNameToClassConstantRector::class => __DIR__ . '/tests/Reflection/ClassNameResolver/ClassNameResolverTest.php',
    ])
    ->withSkip(['*/Fixture/*']);
