<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\ConsoleApplication;

use Entropy\Console\ConsoleApplication;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Exception\InvalidCommandException;
use Entropy\Container\Container;
use PHPUnit\Framework\TestCase;

final class ConsoleApplicationTest extends TestCase
{
    public function testProvideAtLeastOneCommand(): void
    {
        $container = new Container();

        // no command found
        $this->expectException(InvalidCommandException::class);
        $container->make(ConsoleApplication::class);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testValidCommandRegistry(): void
    {
        $container = new Container();

        // load command service from this directory
        $container->autodiscover(__DIR__ . '/Fixture/');
        $container->make(ConsoleApplication::class);
    }

    public function testUnknownOptionIsHandledGracefully(): void
    {
        $container = new Container();
        $container->autodiscover(__DIR__ . '/Fixture/');

        $consoleApplication = $container->make(ConsoleApplication::class);

        // an unknown option must not bubble up as an uncaught exception
        $exitCode = $consoleApplication->run(['entropy', 'test-me', 'some-path', '--unknown']);
        $this->assertSame(ExitCode::ERROR, $exitCode);
    }
}
