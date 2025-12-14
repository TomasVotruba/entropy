<?php

declare(strict_types=1);

namespace Entropy\Console;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Exception\InvalidCommandException;

final class Application
{
    /**
     * @var array<string, CommandInterface>
     */
    private array $commandsByName =[];

    /**
     * @param CommandInterface[] $commands
     */
    public function __construct(
        private InputParser $inputParser,
        private array $commands
    ) {
        foreach ($commands as $command) {
            $name = $command->name();
            if ($name === '') {
                throw new InvalidCommandException('Command name cannot be empty.');
            }
            if (isset($this->commandsByName[$name])) {
                throw new InvalidCommandException(sprintf('Duplicate command name: "%s"', $name));
            }

            $this->commandsByName[$name] = $command;
        }
    }

    /**
     * @param mixed[] $argv
     * @return ExitCode::*
     */
    public function run(array $argv): int
    {
        // run cli command
        // return exit code
        $argumentsAndOptions = $this->inputParser->parse($argv);

        dump($argumentsAndOptions);
        die;

        return ExitCode::SUCCESS;
    }
}
