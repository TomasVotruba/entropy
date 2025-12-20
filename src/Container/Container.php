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

#[RelatedTest(ContainerTest::class)]
final class Container
{
    /**
     * @var array<class-string, callable(Container): object>
     */
    private array $services = [];

    /**
     * @var array<class-string, object>
     */
    private array $instances = [];

    /**
     * @var array<class-string, true>
     */
    private array $making = [];

    /**
     * @var list<class-string>
     */
    private array $makingStack = [];

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

        // setup default console service
        $this->service(CommandRegistry::class, function (Container $container): \Entropy\Console\CommandRegistry {
            $commands = $container->findByContract(CommandInterface::class);
            return new CommandRegistry($commands);
        });
    }

    /**
     * @template TType as object
     *
     * @param class-string<TType> $class
     * @param callable(Container $container): TType $factory
     */
    public function service(string $class, callable $factory): void
    {
        if (isset($this->services[$class])) {
            // avoid service override
            throw new RegisterServiceException(sprintf('Service for "%s" class is already registered', $class));
        }

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

        // circular dependency detection
        if (isset($this->making[$class])) {
            // Build a helpful cycle message: A -> B -> C -> A
            $cycleStartIndex = array_search($class, $this->makingStack, true);
            $cycle = $cycleStartIndex === false
                ? array_merge($this->makingStack, [$class])
                : array_merge(array_slice($this->makingStack, $cycleStartIndex), [$class]);

            throw new CreateServiceException(sprintf(
                'Circular dependency detected:%s"%s"',
                PHP_EOL,
                implode('" -> "', $cycle)
            ));
        }

        // mark as "currently being created"
        $this->making[$class] = true;
        $this->makingStack[] = $class;

        try {
            // factories / registered services
            if (isset($this->services[$class])) {
                $factory = $this->services[$class];

                $instance = $factory($this);
                $this->instances[$class] = $instance;

                return $instance;
            }

            // autowire via reflection
            $reflectionClass = new ReflectionClass($class);

            if ($reflectionClass->isInstantiable()) {
                $instance = $this->createInstance($reflectionClass);
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
            $autodiscovery = new Autodiscovery();

            $autodiscoveredClasses = $autodiscovery->autodiscoverClasses($this->projectDirectory);

            $this->isAutodisovered = true;

            foreach ($autodiscoveredClasses as $autodiscoveredClass) {
                if (isset($this->instances[$autodiscoveredClass])) {
                    continue;
                }

                // register if not yet
                $this->instances[$autodiscoveredClass] = $this->make($autodiscoveredClass);
            }
        }

        $this->warmUpInstanceServices($contractClass);

        return array_filter($this->instances, fn (object $instance): bool => $instance instanceof $contractClass);
    }

    /**
     * @param \ReflectionParameter[] $reflectionParameters
     * @param class-string $class
     * @return array<object>
     */
    private function resolveDependenciesFromParameterReflections(array $reflectionParameters, string $class): array
    {
        $parameterTypes = ParameterTypesResolver::resolve($reflectionParameters, $class);

        $dependencies = [];
        foreach ($parameterTypes as $parameterType) {
            $dependencies[] = $this->make($parameterType);
        }

        return $dependencies;
    }

    private function warmUpInstanceServices(string $contractClass): void
    {
        // warm up instances with registered service of contract
        foreach (array_keys($this->services) as $class) {
            if (! is_a($class, $contractClass, true)) {
                continue;
            }

            if (isset($this->instances[$class])) {
                continue;
            }

            // warm up cache if not yet
            $this->make($class);
        }
    }

    private function createInstance(ReflectionClass $reflectionClass): mixed
    {
        // try to create instance without reflectionParameters
        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            $className = $reflectionClass->getName();
            return new $className();
        }

        // try to resolve dependencies
        $parameters = $constructor->getParameters();
        $dependencies = $this->resolveDependenciesFromParameterReflections($parameters, $reflectionClass->getName());

        // create instance with resolved dependencies
        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
