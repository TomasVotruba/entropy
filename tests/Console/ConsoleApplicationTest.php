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
        $container = new Container();
        $container->service(CommandRegistry::class, function () {
            return new CommandRegistry([]);
        });

        $consoleApplication = $container->make(ConsoleApplication::class);
        $consoleApplication->run([]);
    }
}
