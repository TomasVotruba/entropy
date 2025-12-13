<?php

declare(strict_types=1);

namespace Entropy\Container;

use Entropy\Attributes\RelatedTest;
use Entropy\Exception\CreateServiceException;
use Entropy\FileSystem\FileFinder;
use Entropy\Reflection\ClassNameResolver;
use Entropy\Tests\Container\ContainerTest;
use ReflectionClass;

#[RelatedTest(ContainerTest::class)]
final class Container
{
    /**
     * @var array<class-string, callable(): object>
     */
    private array $services = [];

    /**
     * @var array<class-string, object>
     */
    private array $instances = [];

    private string $projectDirectory;

    private bool $isAutodisovered = false;

    public function __construct(?string $projectDirectory = null)
    {
        if ($projectDirectory === null) {
            $currentDirectory = getcwd();
            $this->projectDirectory = $currentDirectory;
        } else {
            $this->projectDirectory = $projectDirectory;
        }
    }

    /**
     * @template TType as object
     *
     * @param class-string<TType> $class
     * @param callable(): TType $factory
     */
    public function service(string $class, callable $factory): void
    {
        $this->services[$class] = $factory;
    }

    /**
     * @template TType as object
     *
     * @param class-string<TType> $class
     * @return TType
     */
    public function make(string $class): object
    {
        // use cached
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        if (isset($this->services[$class])) {
            // create service here
            $factory = $this->services[$class];

            // pass container itself to factory
            $instance = $factory($this);
            $this->instances[$class] = $instance;

            return $instance;
        }

        $classReflection = new ReflectionClass($class);

        if ($classReflection->isInstantiable()) {
            // try to create instance without reflectionParameters
            $constructor = $classReflection->getConstructor();
            if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
                $instance = new $class();

                // cache
                $this->instances[$class] = $instance;

                return $instance;
            }
            // try to resolve dependencies
            $parameters = $constructor->getParameters();

            $dependencies = $this->resolveDependenciesFromParameterReflections($parameters, $class);

            // create instance with resolved dependencies
            $instance = $classReflection->newInstanceArgs($dependencies);
            $this->instances[$class] = $instance;

            return $instance;

        }

        // @todo create solo instance
        // @todo autowire parameter service

        throw new CreateServiceException(sprintf('No service found for "%s" class', $class));
    }

    /**
     * @template TType as object
     *
     * @param class-string<TType> $contractClass
     * @return array<TType>
     */
    public function findByContract(string $contractClass): array
    {
        if (! $this->isAutodisovered) {
            $autodiscoveredClasses = $this->autodiscoverClasses();
            $this->isAutodisovered = true;

            foreach ($autodiscoveredClasses as $autodiscoveredClass) {
                if (isset($this->instances[$autodiscoveredClass])) {
                    continue;
                }

                // register if not yet
                $this->instances[$autodiscoveredClass] = $this->make($autodiscoveredClass);
            }
        }

        return array_filter($this->instances, fn (object $instance): bool => $instance instanceof $contractClass);
    }

    /**
     * @param \ReflectionParameter[] $reflectionParameters
     * @param class-string $class
     * @return array<object>
     */
    private function resolveDependenciesFromParameterReflections(array $reflectionParameters, string $class): array
    {
        $dependencies = [];

        foreach ($reflectionParameters as $parameter) {
            $parameterType = $parameter->getType();
            if ($parameterType instanceof \ReflectionNamedType && ! $parameterType->isBuiltin()) {
                /** @var class-string $dependencyClass */
                $dependencyClass = $parameterType->getName();
                $dependencies[] = $this->make($dependencyClass);
            } else {
                // cannot resolve non-class parameter
                throw new CreateServiceException(sprintf(
                    'Cannot resolve parameter "%s" for class "%s"',
                    $parameter->getName(),
                    $class
                ));
            }
        }

        return $dependencies;
    }

    /**
     * @return array<class-string>
     */
    private function autodiscoverClasses(): array
    {
        // find alfindByContractl *.php files in given directory using recursive iterator
        $phpFiles = FileFinder::findPhpFiles($this->projectDirectory);

        $classNames = [];

        foreach ($phpFiles as $phpFile) {
            $className = ClassNameResolver::resolveFromFilePath($phpFile);
            if ($className === null) {
                continue;
            }

            $classReflection = new ReflectionClass($className);

            // interface cannot be registered as a service
            if ($classReflection->isInterface()) {
                continue;
            }

            $classNames[] = $className;
        }

        return $classNames;
    }
}
