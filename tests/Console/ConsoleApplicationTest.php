<?php

declare(strict_types=1);

namespace Entropy\Tests\Console;

use Entropy\Console\CommandRegistry;
use Entropy\Console\ConsoleApplication;
use Entropy\Container\Container;
use PHPUnit\Framework\TestCase;

final class ConsoleApplicationTest extends TestCase
{
    public function test(): void
    {
        //        $inputParser = new InputParser();
        //        $helpPrinter = new HelpPrinter();

        $container = new Container();
        $container->service(CommandRegistry::class, function () {
            return new CommandRegistry([]);
        });

        //        $commandRegistry = new CommandRegistry([]);

        $consoleApplication = $container->make(ConsoleApplication::class);
        //        $consoleApplication = new ConsoleApplication($helpPrinter, $inputParser, $commandRegistry);

        $consoleApplication->run([]);
    }
}
