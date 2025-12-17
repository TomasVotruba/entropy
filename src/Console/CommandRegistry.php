<?php

declare(strict_types=1);

namespace Entropy\Console;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Exception\InvalidCommandException;
use Entropy\Utils\FuzzyMatcher;
use Webmozart\Assert\Assert;

final class CommandRegistry
{
    /**
     * @param CommandInterface[] $commands
     */
    public function __construct(
        private array $commands
    ) {
        if ($commands === []) {
            throw new InvalidCommandException('Register at leats one command, so application can run');
        }

        Assert::allIsInstanceOf($commands, CommandInterface::class);

        $existingNames = [];
        foreach ($commands as $command) {
            // make sure the name is registered just once
            if (in_array($command->getName(), $existingNames, true)) {
                throw new InvalidCommandException(sprintf('Duplicate command name: "%s"', $command->getName()));
            }

            $existingNames[] = $command->getName();

            $this->validateCommand($command);
        }
    }

    public function get(string $name): CommandInterface
    {
        if (! $this->has($name)) {
            throw new InvalidCommandException(sprintf(
                'Command not found: "%s". Try one of "%s"',
                $name,
                implode('", "', $this->getCommandsNames())
            ));
        }

        return $this->commands[$name];
    }

    public function getCommandNameMaxLength(): int
    {
        $maxCommandNameLength = 0;
        foreach ($this->commands as $command) {
            $maxCommandNameLength = max($maxCommandNameLength, strlen($command->getName()));
        }

        return $maxCommandNameLength;
    }

    /**
     * @return CommandInterface[]
     */
    public function all(): array
    {
        return $this->commands;
    }

    public function has(string $commandName): bool
    {
        foreach ($this->commands as $command) {
            if ($commandName === $command->getName()) {
                return true;
            }
        }

        $availalbleNames = [];
        foreach ($this->commands as $command) {
            $availalbleNames[] = $command->getName();
        }

        $matchedCommand = FuzzyMatcher::match($commandName, $availalbleNames);
        dump($matchedCommand);
        die;

        //        $nearestName = null;
        //        if (isset($this->commands[$commandName])) {
        //            return true;
        //        }
        //
        //        foreach ($this->commands as $command) {
        //            dump($command);
        //        }

        die;

        return isset($this->commands[$commandName]);
    }

    private function validateCommand(CommandInterface $command): void
    {
        $name = $command->getName();
        if ($name === '') {
            throw new InvalidCommandException('Command name cannot be empty');
        }

        if ($command->getDescription() === '') {
            throw new InvalidCommandException('Command description cannot be empty');
        }

        if (! method_exists($command, 'run')) {
            throw new InvalidCommandException(sprintf('Command "%s" must have a public "run()" method', $name));
        }
    }

    /**
     * @return string[]
     */
    private function getCommandsNames(): array
    {
        $commandNames = [];

        foreach ($this->commands as $command) {
            $commandNames[] = $command->getName();
        }

        return $commandNames;
    }
}
