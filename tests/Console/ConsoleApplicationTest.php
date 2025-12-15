<?php

declare(strict_types=1);

namespace Entropy\Tests\Console;

use Entropy\Console\CommandRegistry;
use Entropy\Console\ConsoleApplication;
use Entropy\Console\Exception\InvalidCommandException;
use Entropy\Container\Container;
use PHPUnit\Framework\TestCase;

final class ConsoleApplicationTest extends TestCase
{
    public function testProvideAtLeastOneCommand(): void
    {
        $container = new Container();
        $container->service(CommandRegistry::class, function () {
            return new CommandRegistry([]);
        });

        $this->expectException(InvalidCommandException::class);
        $container->make(ConsoleApplication::class);
    }
}
