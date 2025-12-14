<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Autodiscovery;

use App\Project\Command\OtherCommand;
use App\Project\Contract\CommandInterface;
use App\Project\Contract\ServiceTypeInterface;
use App\Project\Contract\SomeContract;
use App\Project\SomeApplication;
use Entropy\Container\Container;
use PHPUnit\Framework\TestCase;

final class AutodiscoveryTest extends TestCase
{
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

    public function testSkipValueObjects(): void
    {
        $container = new Container(__DIR__ . '/Fixture/project-with-value-objects');
        $someContracts = $container->findByContract(SomeContract::class);

        $this->assertCount(0, $someContracts);
    }

    public function testUseRegisteredService(): void
    {
        $container = new Container(__DIR__ . '/Fixture/project-with-value-objects');

        $container->service(OtherCommand::class, function (Container $container): OtherCommand {
            $serviceTypes = $container->findByContract(ServiceTypeInterface::class);
            return new OtherCommand($serviceTypes);
        });

        $container->service(SomeApplication::class, function (Container $container): SomeApplication {
            $commands = $container->findByContract(CommandInterface::class);
            return new SomeApplication($commands);
        });

        // here the command should be present, as we registered it above
        $someApplication = $container->make(SomeApplication::class);
        $this->assertCount(1, $someApplication->getCommands());
    }
}
