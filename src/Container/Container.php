<?php

declare(strict_types=1);

namespace Entropy\Container;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\CommandRegistry;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Container\Exception\CreateServiceException;
use Entropy\Container\Exception\RegisterServiceException;
use Entropy\Reflection\ParameterTypesResolver;
use Entropy\Tests\Container\Container\ContainerTest;
use ReflectionClass;
use Webmozart\Assert\Assert;

#[RelatedTest(ContainerTest::class)]
final class Container
{
    /**
     * @var array<class-string, callable(Container): object>
     */
    private array $serviceFactories = [];

    /**
     * @var array<class-string, object>
     */
    private array $instances = [];

    /**
     * Detects circular dependencies
     * @var array<class-string, true>
     */
    private array $making = [];

    /**
     * Detects circular dependencies
     * @var list<class-string>
     */
    private array $makingStack = [];

    private bool $isAutodisovered = false;

    /**
     * @var string[]
     */
    private array $autodiscoveryDirectories = [];

    public function __construct()
    {
        // setup default console service

        $this->service(CommandRegistry::class, function (Container $container): \Entropy\Console\CommandRegistry {
            $commands = $container->findByContract(CommandInterface::class);
            return new CommandRegistry($commands);
        });
    }

    /**
     * Register service from provided directory
     */
    public function autodiscover(string $directory): void
    {
        // must be trigged on lazy call of findByContract, to make sure all services are registered
        $this->autodiscoveryDirectories[] = $directory;
    }

    /**
     * @template TType as object
     *
     * @param class-string<TType> $class
     * @param callable(Container $container): TType $factory
     */
    public function service(string $class, callable $factory): void
    {
        if (isset($this->serviceFactories[$class])) {
            // avoid service override
            throw new RegisterServiceException(sprintf('Service for "%s" class is already registered', $class));
        }

        $this->serviceFactories[$class] = $factory;
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

        // circular dependency detection
        if (isset($this->making[$class])) {
            // Build a helpful cycle message: A -> B -> C -> A
            $cycleStartIndex = array_search($class, $this->makingStack, true);
            $cycle = $cycleStartIndex === false
                ? array_merge($this->makingStack, [$class])
                : array_merge(array_slice($this->makingStack, $cycleStartIndex), [$class]);

            throw new CreateServiceException(sprintf('Circular dependency detected: %s', implode(' -> ', $cycle)));
        }

        // mark as "currently being created"
        $this->making[$class] = true;
        $this->makingStack[] = $class;

        try {
            // factories / registered services
            if (isset($this->serviceFactories[$class])) {
                $factory = $this->serviceFactories[$class];

                $instance = $factory($this);
                $this->instances[$class] = $instance;

                return $instance;
            }

            // autowire via reflection
            $reflectionClass = new ReflectionClass($class);

            if ($reflectionClass->isInstantiable()) {
                $instance = $this->createInstanceFromReflection($reflectionClass);
                $this->instances[$class] = $instance;

                return $instance;
            }

            throw new CreateServiceException(sprintf('No service found for "%s" class', $class));
        } finally {
            // always unmark, even if construction throws
            array_pop($this->makingStack);
            unset($this->making[$class]);
        }
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
            foreach ($this->autodiscoveryDirectories as $autodiscoveryDirectory) {
                $this->autodiscoverDirectory($autodiscoveryDirectory);
            }

            $this->isAutodisovered = true;
        }

        $this->warmUpInstanceServices($contractClass);

        return array_filter($this->instances, fn (object $instance): bool => $instance instanceof $contractClass);
    }

    private function autodiscoverDirectory(string $directory): void
    {
        Assert::directory($directory);

        $autodiscovery = new Autodiscovery();
        $serviceClassNames = $autodiscovery->autodiscoverDirectory($directory);

        foreach ($serviceClassNames as $serviceClassName) {
            if (isset($this->instances[$serviceClassName])) {
                continue;
            }

            if (isset($this->serviceFactories[$serviceClassName])) {
                continue;
            }

            $this->instances[$serviceClassName] = $this->make($serviceClassName);
        }
    }

    /**
     * @param \ReflectionParameter[] $reflectionParameters
     * @param class-string $class
     * @return array<object|object[]>
     */
    private function resolveDependenciesFromParameterReflections(
        \ReflectionMethod $reflectionMethod,
        array $reflectionParameters,
        string $class
    ): array {
        $parameterTypes = ParameterTypesResolver::resolve($reflectionMethod, $reflectionParameters, $class);

        $dependencies = [];
        foreach ($parameterTypes as $parameterType) {
            $dependencies[] = is_array($parameterType) ? $this->findByContract($parameterType[0]) : $this->make(
                $parameterType
            );
        }

        return $dependencies;
    }

    private function warmUpInstanceServices(string $contractClass): void
    {
        // warm up instances with registered service of contract
        foreach (array_keys($this->serviceFactories) as $class) {
            if (! is_a($class, $contractClass, true)) {
                continue;
            }

            if (isset($this->instances[$class])) {
                continue;
            }

            // warm up cache if not yet
            $this->instances[$class] = $this->make($class);
        }
    }

    private function createInstanceFromReflection(ReflectionClass $reflectionClass): object
    {
        // try to create instance without reflectionParameters
        $constructorReflection = $reflectionClass->getConstructor();
        if ($constructorReflection === null || $constructorReflection->getNumberOfParameters() === 0) {
            $className = $reflectionClass->getName();
            return new $className();
        }

        // try to resolve dependencies
        $parameters = $constructorReflection->getParameters();
        $dependencies = $this->resolveDependenciesFromParameterReflections(
            $constructorReflection,
            $parameters,
            $reflectionClass->getName()
        );

        // create instance with resolved dependencies
        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
