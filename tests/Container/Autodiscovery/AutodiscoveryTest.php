<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Autodiscovery;

use App\Project\Contract\CommandInterface;
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

    public function testAutodiscoveryOfValueObjects(): void
    {
        $container = new Container(__DIR__ . '/Fixture/project-with-value-objects');
        $commands = $container->findByContract(CommandInterface::class);

        $this->assertCount(0, $commands);
    }
}
