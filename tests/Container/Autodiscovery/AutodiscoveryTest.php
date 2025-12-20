<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Autodiscovery;

use App\Project\Command\OtherCommand;
use App\Project\Contract\CommandInterface;
use App\Project\Contract\ServiceTypeInterface;
use App\Project\Contract\SomeContract;
use Entropy\Console\CommandRegistry;
use Entropy\Container\Container;
use PHPUnit\Framework\TestCase;

final class AutodiscoveryTest extends TestCase
{
    public function testContractAutodiscovery(): void
    {
        $container = new Container();
        $container->autodiscover(__DIR__ . '/Fixture/project-directory');

        $commands = $container->findByContract(CommandInterface::class);
        $this->assertCount(1, $commands);

        $this->assertContainsOnlyInstancesOf(CommandInterface::class, $commands);
    }

    public function testSkipValueObjects(): void
    {
        $container = new Container();
        $someContracts = $container->findByContract(SomeContract::class);

        $this->assertCount(0, $someContracts);
    }

    public function testUseRegisteredService(): void
    {
        $container = new Container();
        $container->autodiscover(__DIR__ . '/Fixture/project-with-value-objects/src/ServiceType');
        $container->autodiscover(__DIR__ . '/Fixture/project-with-value-objects/src/Command');

        // @todo enable autowire of param by type[] docblock
        $container->service(OtherCommand::class, function (Container $container): OtherCommand {
            $serviceTypes = $container->findByContract(ServiceTypeInterface::class);
            return new OtherCommand($serviceTypes);
        });

        // here the command should be present, as we registered it above
        $commandRegistry = $container->make(CommandRegistry::class);
        $this->assertCount(1, $commandRegistry->all());
    }
}
