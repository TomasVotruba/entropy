<?php

declare(strict_types=1);

namespace Entropy\Reflection;

use Entropy\Container\Exception\CreateServiceException;

final class ParameterTypesResolver
{
    /**
     * @param \ReflectionParameter[] $reflectionParameters
     * @param class-string $class
     *
     * @return array<class-string>
     */
    public static function resolve(array $reflectionParameters, string $class): array
    {
        $parameterTypes = [];

        foreach ($reflectionParameters as $parameter) {
            $parameterType = $parameter->getType();
            if ($parameterType instanceof \ReflectionNamedType && ! $parameterType->isBuiltin()) {
                /** @var class-string $dependencyClass */
                $parameterTypes[] = $parameterType->getName();
            } else {
                // cannot resolve non-class parameter
                throw new CreateServiceException(sprintf(
                    'Cannot resolve parameter "%s" for class "%s"',
                    $parameter->getName(),
                    $class
                ));
            }
        }

        return $parameterTypes;
    }

}
