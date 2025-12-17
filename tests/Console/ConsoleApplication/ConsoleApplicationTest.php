<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\ConsoleApplication;

use Entropy\Console\CommandRegistry;
use Entropy\Console\ConsoleApplication;
use Entropy\Console\Exception\InvalidCommandException;
use Entropy\Container\Container;
use Entropy\Tests\Console\ConsoleApplication\Fixture\SimpleCommand;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

final class ConsoleApplicationTest extends TestCase
{
    public function testProvideAtLeastOneCommand(): void
    {
        $container = new Container();
//        $container->service(CommandRegistry::class, fn (): \Entropy\Console\CommandRegistry => new CommandRegistry([]));

        $this->expectException(InvalidCommandException::class);
        $container->make(ConsoleApplication::class);
    }

    #[DoesNotPerformAssertions]
    public function testValidCommandRegistry(): void
    {
        $container = new Container();
        $container->service(CommandRegistry::class, function (Container $container): \Entropy\Console\CommandRegistry {
            $simpleCommand = $container->make(SimpleCommand::class);

            return new CommandRegistry([$simpleCommand]);
        });

        $container->make(ConsoleApplication::class);
    }
}
