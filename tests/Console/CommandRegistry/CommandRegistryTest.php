<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\CommandRegistry;

use Entropy\Console\CommandRegistry;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Tests\Console\CommandRegistry\Fixture\DefaultFixtureCommand;
use Entropy\Tests\Console\CommandRegistry\Fixture\HiddenFixtureCommand;
use Entropy\Tests\Console\CommandRegistry\Fixture\RegularFixtureCommand;
use PHPUnit\Framework\TestCase;

final class CommandRegistryTest extends TestCase
{
    private CommandRegistry $commandRegistry;

    protected function setUp(): void
    {
        $this->commandRegistry = new CommandRegistry([
            new DefaultFixtureCommand(),
            new HiddenFixtureCommand(),
            new RegularFixtureCommand(),
        ]);
    }

    public function testGetDefaultReturnsDefaultCommand(): void
    {
        $defaultCommand = $this->commandRegistry->getDefault();

        $this->assertInstanceOf(DefaultFixtureCommand::class, $defaultCommand);
    }

    public function testGetVisibleExcludesHiddenCommands(): void
    {
        $visibleCommands = $this->commandRegistry->getVisible();

        $this->assertCount(2, $visibleCommands);

        $visibleNames = array_map(
            static fn (CommandInterface $command): string => $command->getName(),
            $visibleCommands
        );
        $this->assertContains('check', $visibleNames);
        $this->assertContains('list-checkers', $visibleNames);
        $this->assertNotContains('worker', $visibleNames);
    }

    public function testAllKeepsHiddenCommands(): void
    {
        $this->assertCount(3, $this->commandRegistry->all());
    }
}
