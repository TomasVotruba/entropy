<?php

declare(strict_types=1);

namespace Entropy\Console;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Exception\InvalidCommandException;
use Webmozart\Assert\Assert;

final class CommandRegistry
{
    /**
     * @var array<non-empty-string, CommandInterface>
     */
    private array $commandsByName;

    /**
     * @param CommandInterface[] $commands
     */
    public function __construct(array $commands)
    {
        Assert::notEmpty($commands);
        Assert::allIsInstanceOf($commands, CommandInterface::class);

        foreach ($commands as $command) {
            $this->validateCommand($command);

            $this->commandsByName[$command->getName()] = $command;
        }
    }

    public function get(string $name): CommandInterface
    {
        if (! $this->commandsByName[$name]) {
            throw new InvalidCommandException(sprintf('Command not found: "%s"', $name));
        }

        return $this->commandsByName[$name];
    }

    public function getCommandNameMaxLength(): int
    {
        $maxCommandNameLength = 0;
        foreach ($this->commandsByName as $command) {
            $maxCommandNameLength = max($maxCommandNameLength, strlen($command->getName()));
        }

        return $maxCommandNameLength;
    }

    /**
     * @return CommandInterface[]
     */
    public function all(): array
    {
        return $this->commandsByName;
    }

    public function has(string $commandName): bool
    {
        return isset($this->commandsByName[$commandName]);
    }

    private function validateCommand(CommandInterface $command): void
    {
        $name = $command->getName();
        if ($name === '') {
            throw new InvalidCommandException('Command name cannot be empty');
        }

        if (isset($this->commandsByName[$name])) {
            throw new InvalidCommandException(sprintf('Duplicate command name: "%s"', $name));
        }

        // same for description
        $description = $command->getDescription();
        if ($description === '') {
            throw new InvalidCommandException('Command description cannot be empty');
        }
    }
}
