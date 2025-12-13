<?php

declare(strict_types=1);

namespace Entropy\Tests\Container;

use Entropy\Container\Container;
use Entropy\Tests\Container\Fixture\SomeType;
use Entropy\Tests\Container\Fixture\WithDependencies;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    public function testRegisterServiceAndCache(): void
    {
        $container = new Container();
        $container->service(SomeType::class, function (): SomeType {
            return new SomeType();
        });

        $firstSomeType = $container->make(SomeType::class);
        $secondSomeType = $container->make(SomeType::class);

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
}
