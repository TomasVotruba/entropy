<?php

declare(strict_types=1);

namespace Entropy\Tests\Reflection\ValueOptionNameResolver;

use Entropy\Reflection\ValueOptionNameResolver;
use Entropy\Tests\Reflection\ValueOptionNameResolver\Fixture\SomeCommandWithOptions;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class ValueOptionNameResolverTest extends TestCase
{
    public function test(): void
    {
        $reflectionMethod = new ReflectionMethod(SomeCommandWithOptions::class, 'run');

        $valueOptionNames = ValueOptionNameResolver::resolve($reflectionMethod);

        // bool flags "clear-cache" and "fix" are excluded; plural array is singularised
        $this->assertSame([
            'paths' => true,
            'skip-file' => true,
            'config' => true,
            'limit' => true,
        ], $valueOptionNames);
    }
}
