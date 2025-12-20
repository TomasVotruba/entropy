<?php

declare(strict_types=1);

namespace Entropy\Container;

use Entropy\Attributes\RelatedTest;
use Entropy\FileSystem\FileFinder;
use Entropy\Reflection\ClassNameResolver;
use Entropy\Tests\Container\Autodiscovery\AutodiscoveryTest;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Webmozart\Assert\Assert;

/**
 * Registers project classes to services automatically
 */
#[RelatedTest(AutodiscoveryTest::class)]
final class Autodiscovery
{
    /**
     * @return array<class-string>
     */
    public function autodiscoverDirectory(string $directory): array
    {
        $phpFiles = FileFinder::findPhpFiles($directory);
        return $this->resolveClassNames($phpFiles);
    }

    /**
     * @return array<class-string>
     */
    public function autodiscoverProjectClasses(string $projectDirectory): array
    {
        // find alfindByContractl *.php files in given directory using recursive iterator
        $phpFiles = FileFinder::findSourcePhpFiles($projectDirectory);
        return $this->resolveClassNames($phpFiles);
    }

    /**
     * Class constructor must be with all parameters typed as classes
     * (no built-in scalar types, no untyped)
     */
    private function hasAllParametersWithTypedClasses(ReflectionClass $reflectionClass): bool
    {
        $constructorReflection = $reflectionClass->getConstructor();
        if (! $constructorReflection instanceof ReflectionMethod) {
            return true;
        }

        $parameters = $constructorReflection->getParameters();
        foreach ($parameters as $parameter) {
            $parameterType = $parameter->getType();
            if (! $parameterType instanceof ReflectionNamedType) {
                return false;
            }

            // e.g. DateTime is not a service
            if ($parameterType->isBuiltin()) {
                return false;
            }
        }

        return true;
    }

    private function shouldSkipClass(string $className): bool
    {
        // @todo exclude classes with ValueObject, DTO, Enum, Exception in their namespace
        // those are not services

        $reflectionClass = new ReflectionClass($className);

        // interface cannot be registered as a service
        if ($reflectionClass->isInterface()) {
            return true;
        }

        if ($reflectionClass->isSubclassOf(\Throwable::class)) {
            return true;
        }

        if ($reflectionClass->isEnum()) {
            return true;
        }

        // no parent class/interface, nothing to register
        if ($reflectionClass->getParentClass() === false && $reflectionClass->getInterfaceNames() === []) {
            return true;
        }

        // has all parameter with typed class dependencies
        return ! $this->hasAllParametersWithTypedClasses($reflectionClass);
    }

    /**
     * @param string[] $phpFiles
     *
     * @return string[]
     */
    private function resolveClassNames(array $phpFiles): array
    {
        Assert::allString($phpFiles);

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
}
