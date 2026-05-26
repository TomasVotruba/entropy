<?php

declare(strict_types=1);

namespace Entropy\Tests\Reflection\ParameterOptionMarkerResolver;

use Entropy\Reflection\ParameterOptionMarkerResolver;
use Entropy\Tests\Reflection\ParameterOptionMarkerResolver\Fixture\SomeClassWithOptionMarker;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class ParameterOptionMarkerResolverTest extends TestCase
{
    public function test(): void
    {
        $reflectionMethod = new ReflectionMethod(SomeClassWithOptionMarker::class, 'someMethod');

        $markers = ParameterOptionMarkerResolver::resolve($reflectionMethod);

        $this->assertSame([
            'source' => true,
        ], $markers);
    }
}
