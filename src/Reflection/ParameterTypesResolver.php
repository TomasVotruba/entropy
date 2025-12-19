<?php

declare(strict_types=1);

namespace Entropy\Reflection;

use Entropy\Attributes\RelatedTest;
use Entropy\Container\Exception\CreateServiceException;
use Entropy\Tests\Reflection\ParameterTypesResolver\ParameterTypesResolverTest;
use ReflectionParameter;

#[RelatedTest(ParameterTypesResolverTest::class)]
final class ParameterTypesResolver
{
    /**
     * @param ReflectionParameter[] $reflectionParameters
     * @param class-string $class
     *
     * @return class-string[]
     */
    public static function resolve(array $reflectionParameters, string $class): array
    {
        $parameterTypes = [];

        foreach ($reflectionParameters as $reflectionParameter) {
            $parameterType = $reflectionParameter->getType();

            if ($parameterType instanceof \ReflectionNamedType && ! $parameterType->isBuiltin()) {
                $parameterTypes[] = $parameterType->getName();
                // skip default value as not required
            } elseif ($reflectionParameter->isDefaultValueAvailable()) {
                continue;
            } else {
                // cannot resolve non-class parameter
                throw new CreateServiceException(sprintf(
                    'Cannot resolve parameter "%s" for class "%s"',
                    $reflectionParameter->getName(),
                    $class
                ));
            }
        }

        return $parameterTypes;
    }
}
