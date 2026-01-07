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
    ->withRootFiles()
    ->withPhpSets()
    ->withImportNames()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        naming: true,
        earlyReturn: true,
        rectorPreset: true
    )
    ->withSkip([
        // false-positive validation
//        RemoveEmptyClassMethodRector::class => __DIR__ . '/src/Attributes',

        // testing string to class name resolution
        StringClassNameToClassConstantRector::class => __DIR__ . '/tests/Reflection/ClassNameResolver/ClassNameResolverTest.php',
    ])
    ->withSkip(['*/Fixture/*']);
