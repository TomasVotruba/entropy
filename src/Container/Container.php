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
        $this->service(CommandRegistry::class, function (Container $container) {
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
            $instance = $this->createInstance($classReflection);
            $this->instances[$class] = $instance;

            return $instance;
        }

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

    private function createInstance(ReflectionClass $classReflection): mixed
    {
        // try to create instance without reflectionParameters
        $constructor = $classReflection->getConstructor();
        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            $className = $classReflection->getName();
            return new $className();
        }

        // try to resolve dependencies
        $parameters = $constructor->getParameters();
        $dependencies = $this->resolveDependenciesFromParameterReflections($parameters, $classReflection->getName());

        // create instance with resolved dependencies
        return $classReflection->newInstanceArgs($dependencies);
    }
}
