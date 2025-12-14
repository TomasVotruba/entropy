<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Container;

use App\Project\Contract\CommandInterface;
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
        $container->service(SomeType::class, function (): SomeType {
            return new SomeType();
        });

        // fetch services
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

    public function testContractAutodiscovery(): void
    {
        // find all by types without registration
        // trigger on findAllByType() ...
        // scan through src/app directories and register services automatically :)

        $container = new Container(__DIR__ . '/Fixture/project-directory');

        $commands = $container->findByContract(CommandInterface::class);
        $this->assertCount(1, $commands);

        $this->assertContainsOnlyInstancesOf(CommandInterface::class, $commands);
    }

    public function testAutodiscoveryOfValueObjects(): void
    {
        $container = new Container(__DIR__ . '/Fixture/project-with-value-objects');
        $commands = $container->findByContract(CommandInterface::class);

        $this->assertCount(0, $commands);
    }

    public function testContainerPassInClosure(): void
    {
        $container = new Container();
        $container->service(SomeType::class, function (Container $container): SomeType {
            return new SomeType();
        });

        $someType = $container->make(SomeType::class);
        $this->assertInstanceOf(SomeType::class, $someType);
    }

    public function testOverride(): void
    {
        $container = new Container();
        $container->service(SomeType::class, function (): SomeType {
            return new SomeType();
        });

        $this->expectException(RegisterServiceException::class);

        // try to override service
        $container->service(SomeType::class, function (): SomeType {
            return new SomeType();
        });
    }
}
