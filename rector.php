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
    ->withImportNames()
    ->withPreparedSets(
        deadCode: true,
        codingStyle: true,
        codeQuality: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        naming: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withRules([
        \Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector::class,
        \Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector::class,
    ])
    ->withSkip([
        // false-positive validation
        RemoveUnusedConstructorParamRector::class => __DIR__ . '/src/Attributes',
        RemoveEmptyClassMethodRector::class => __DIR__ . '/src/Attributes',

        // testing string to class name resolution
        StringClassNameToClassConstantRector::class => __DIR__ . '/tests/Reflection/ClassNameResolver/ClassNameResolverTest.php',
    ])
    ->withSkip(['*/Fixture/*']);
