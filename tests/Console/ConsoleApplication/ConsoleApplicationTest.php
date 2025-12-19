<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\ConsoleApplication;

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
        $container = new Container(__DIR__);

        // no command found
        $this->expectException(InvalidCommandException::class);
        $container->make(ConsoleApplication::class);
    }

    #[DoesNotPerformAssertions]
    public function testValidCommandRegistry(): void
    {
        $container = new Container(__DIR__);

        $container->service(
            SimpleCommand::class,
            fn (): SimpleCommand => new SimpleCommand()
        );

        $container->make(ConsoleApplication::class);
    }
}
