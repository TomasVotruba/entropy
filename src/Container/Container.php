<?php

declare(strict_types=1);

namespace Entropy\Container;

use Entropy\Attributes\RelatedTest;
use Entropy\Exception\CreateServiceException;
use Entropy\Tests\Container\ContainerTest;

#[RelatedTest(ContainerTest::class)]
final class Container
{
    /**
     * @var array<class-string, callable()>
     */
    private array $services = [];

    /**
     * @var array<class-string, object>
     */
    private array $instances = [];

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
            $instance = $factory();

            $this->instances[$class] = $instance;

            return $instance;
        }

        $classReflection = new \ReflectionClass($class);

        if ($classReflection->isInstantiable()) {
            // try to create instance without parameters
            $constructor = $classReflection->getConstructor();
            if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
                $instance = new $class();

                // cache
                $this->instances[$class] = $instance;

                return $instance;
            }
        }

        // @todo create solo instance
        // @todo autowire parameter service

        throw new CreateServiceException(sprintf('No service found for "%s" class', $class));
    }
}
