<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withRootFiles()
    ->withPhpSets()
    ->withImportNames()
    ->withPreparedSets(
        true,  // deadCode
        true,  // codeQuality
        true,  // codingStyle
        true,  // typeDeclarations
        true,  // typeDeclarationDocblocks
        true,  // privatization
        true,  // naming
        false, // namedArgs
        false, // instanceOf
        false, // if
        true,  // earlyReturn
        false, // carbon
        true   // rectorPreset
    )
    ->withSkip([
        // testing string to class name resolution
        StringClassNameToClassConstantRector::class => __DIR__ . '/tests/Reflection/ClassNameResolver/ClassNameResolverTest.php',
    ])
    ->withSkip(['*/Fixture/*']);
