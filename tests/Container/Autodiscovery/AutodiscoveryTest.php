<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Autodiscovery;

use App\Project\Contract\CommandInterface;
use App\Project\Contract\SomeContract;
use App\Project\SomeApplication;
use App\Project\SomeCommand;
use Entropy\Console\Application;
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

        $container->service(SomeCommand::class, function (): SomeCommand {
            return new SomeCommand();
        });

        $container->service(SomeApplication::class, function (Container $container): SomeApplication {
            $commands = $container->findByContract(CommandInterface::class);
            return new SomeApplication($commands);
        });

        $someApplication = $container->make(SomeApplication::class);
        $this->assertCount(1, $someApplication->getCommands());
    }
}
