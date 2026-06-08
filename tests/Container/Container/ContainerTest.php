<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Container;

use Entropy\Container\Container;
use Entropy\Container\Exception\RegisterServiceException;
use Entropy\Tests\Container\Container\Fixture\SomeType;
use Entropy\Tests\Container\Container\Fixture\WithDependencies;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    public function testRegisterServiceAndCache(): void
    {
        $container = new Container();
        $container->service(SomeType::class, fn (): SomeType => new SomeType());

        // fetch services
        $firstSomeType = $container->make(SomeType::class);
        $secondSomeType = $container->make(SomeType::class);

        // must be the same instance
        $this->assertInstanceOf(SomeType::class, $firstSomeType);
        $this->assertSame($firstSomeType, $secondSomeType);
    }

    public function testCreateServiceWithoutRegistration(): void
    {
        $container = new Container();

        $someType = $container->make(SomeType::class);
        $this->assertInstanceOf(SomeType::class, $someType);
    }

    public function testCreateServiceWithDependencies(): void
    {
        $container = new Container();

        $withDependencies = $container->make(WithDependencies::class);
        $this->assertInstanceOf(WithDependencies::class, $withDependencies);
    }

    public function testCreateServiceWithDependenciesThenMakeTheSameService(): void
    {
        $container = new Container();

        $withDependencies = $container->make(WithDependencies::class);
        $this->assertInstanceOf(WithDependencies::class, $withDependencies);

        $containerFetchedSomeType = $container->make(SomeType::class);
        $serviceFetcheSomeType = $withDependencies->getSomeType();

        $this->assertSame($serviceFetcheSomeType, $containerFetchedSomeType);
    }

    public function testContainerPassInClosure(): void
    {
        $container = new Container();
        $container->service(SomeType::class, fn (Container $container): SomeType => new SomeType());

        $someType = $container->make(SomeType::class);
        $this->assertInstanceOf(SomeType::class, $someType);
    }

    public function testPreventServiceOverride(): void
    {
        $container = new Container();
        $container->service(SomeType::class, fn (): SomeType => new SomeType());

        $this->expectException(RegisterServiceException::class);

        // try to override service
        $container->service(SomeType::class, fn (): SomeType => new SomeType());
    }

    public function testBuildReturnsFreshInstanceWithoutCaching(): void
    {
        $container = new Container();

        $firstSomeType = $container->build(SomeType::class);
        $secondSomeType = $container->build(SomeType::class);

        $this->assertInstanceOf(SomeType::class, $firstSomeType);
        // build() never caches, so each call is a new instance
        $this->assertNotSame($firstSomeType, $secondSomeType);
    }

    public function testBuildResolvesDependencies(): void
    {
        $container = new Container();

        $withDependencies = $container->build(WithDependencies::class);
        $this->assertInstanceOf(WithDependencies::class, $withDependencies);
        $this->assertInstanceOf(SomeType::class, $withDependencies->getSomeType());
    }
}
