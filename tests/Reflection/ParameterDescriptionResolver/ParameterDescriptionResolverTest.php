<?php

declare(strict_types=1);

namespace Entropy\Tests\Reflection\ParameterDescriptionResolver;

use Entropy\Reflection\ParameterDescriptionResolver;
use PHPUnit\Framework\TestCase;

final class ParameterDescriptionResolverTest extends TestCase
{
    public function test(): void
    {
        $someMethodReflection = new \ReflectionMethod(\Entropy\Tests\Reflection\ParameterDescriptionResolver\Fixture\SomeClassWithMethod::class, 'someMethod');
        $parameterDescriptions = ParameterDescriptionResolver::resolve($someMethodReflection);

        $this->assertSame([
            'isEnabled' => 'Description of the option'
        ], $parameterDescriptions);
    }
}
