<?php

declare(strict_types=1);

namespace Entropy\Tests\Console;

use Entropy\Console\CommandRegistry;
use Entropy\Console\ConsoleApplication;
use Entropy\Console\Exception\InvalidCommandException;
use Entropy\Container\Container;
use Entropy\Tests\Console\Fixture\SimpleCommand;
use PHPUnit\Framework\TestCase;

final class ConsoleApplicationTest extends TestCase
{
    public function testProvideAtLeastOneCommand(): void
    {
        $container = new Container();
        $container->service(CommandRegistry::class, fn (): \Entropy\Console\CommandRegistry => new CommandRegistry([]));

        $this->expectException(InvalidCommandException::class);
        $container->make(ConsoleApplication::class);
    }

    public function testValidCommandRegistry(): void
    {
        $container = new Container();
        $container->service(CommandRegistry::class, function (Container $container): \Entropy\Console\CommandRegistry {
            $simpleCommand = $container->make(SimpleCommand::class);

            return new CommandRegistry([$simpleCommand]);
        });

        $this->expectException(InvalidCommandException::class);
        $container->make(ConsoleApplication::class);

    }
}
