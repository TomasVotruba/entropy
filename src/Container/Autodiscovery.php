<?php

declare(strict_types=1);

namespace Entropy\Container;

use Entropy\FileSystem\FileFinder;
use Entropy\Reflection\ClassNameResolver;
use ReflectionClass;

/**
 * Load project classes to services automatically
 */
final class Autodiscovery
{
    /**
     * @return array<class-string>
     */
    public function autodiscoverClasses(string $projectDirectory): array
    {
        // find alfindByContractl *.php files in given directory using recursive iterator
        $phpFiles = FileFinder::findSourcePhpFiles($projectDirectory);

        $classNames = [];

        foreach ($phpFiles as $phpFile) {
            $className = ClassNameResolver::resolveFromFilePath($phpFile);
            if ($className === null) {
                continue;
            }

            if ($this->shouldSkipClass($className)) {
                continue;
            }

            $classNames[] = $className;
        }

        return $classNames;
    }

    /**
     * Class constructor must be with all parameters typed as classes
     * (no built-in scalar types, no untyped)
     */
    private function hasAllParametersWithTypedClasses(ReflectionClass $classReflection): bool
    {
        $constructorReflection = $classReflection->getConstructor();
        if ($constructorReflection !== null) {
            $parameters = $constructorReflection->getParameters();
            foreach ($parameters as $parameter) {
                $parameterType = $parameter->getType();
                if ($parameterType instanceof \ReflectionType && $parameterType->isBuiltin()) {
                    return false;
                }

                if (! $parameterType instanceof \ReflectionNamedType) {
                    return false;
                }
            }
        }

        return true;
    }

    private function shouldSkipClass(string $className): bool
    {
        $classReflection = new ReflectionClass($className);

        // interface cannot be registered as a service
        if ($classReflection->isInterface()) {
            return true;
        }

        if ($classReflection->isSubclassOf(\Throwable::class)) {
            return true;
        }

        if ($classReflection->isEnum()) {
            return true;
        }

        // no parent class/interface, nothing to register
        if ($classReflection->getParentClass() === false && $classReflection->getInterfaceNames() === []) {
            return true;
        }

        // has all parameter with typed class dependencies
        return ! $this->hasAllParametersWithTypedClasses($classReflection);
    }
}
