<?php

declare(strict_types=1);

namespace Entropy\Tests\Reflection\ParameterTypesResolver;

use Entropy\Reflection\ParameterTypesResolver;
use Entropy\Tests\Reflection\ParameterTypesResolver\Fixture\AnotherClass;
use Entropy\Tests\Reflection\ParameterTypesResolver\Fixture\SomeMethodWithTypes;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class ParameterTypesResolverTest extends TestCase
{
    public function test(): void
    {
        $reflectionMethod = new ReflectionMethod(SomeMethodWithTypes::class, 'run');

        $parameterObjectTypes = ParameterTypesResolver::resolve(
            $reflectionMethod,
            $reflectionMethod->getParameters(),
            SomeMethodWithTypes::class
        );

        $this->assertSame([AnotherClass::class], $parameterObjectTypes);
    }
}
