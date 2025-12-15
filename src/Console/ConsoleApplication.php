<?php

declare(strict_types=1);

namespace Entropy\Console;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Exception\InvalidCommandException;

final class ConsoleApplication
{
    /**
     * @var array<string, CommandInterface>
     */
    private array $commandsByName = [];

    /**
     * @param CommandInterface[] $commands
     */
    public function __construct(
        private InputParser $inputParser,
        array $commands
    ) {
        foreach ($commands as $command) {
            $name = $command->getName();
            if ($name === '') {
                throw new InvalidCommandException('Command getName cannot be empty.');
            }
            if (isset($this->commandsByName[$name])) {
                throw new InvalidCommandException(sprintf('Duplicate command getName: "%s"', $name));
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
        $argumentsAndOptions = $this->inputParser->parse($argv);

        // global help
        if (in_array('--help', $argv, true) || in_array('-h', $argv, true)) {
            $this->printHelp();
            return 0;
        }

        $commandName = $argumentsAndOptions->getCommandName();
        if (! isset($this->commandsByName[$commandName])) {
            fwrite(STDERR, sprintf(
                "Unknown command: %s\n\n Available commands are:\n%s",
                $commandName,
                implode(
                    "\n",
                    array_map(fn (CommandInterface $command) => '  - ' . $command->getName(), $this->commandsByName)
                )
            ));

            $this->printHelp();
            return ExitCode::INVALID_COMMAND;
        }

        try {
            $command = $this->commandsByName[$commandName];
            return $command->run($argumentsAndOptions);
        } catch (\Throwable $throwable) {
            fwrite(STDERR, "Unhandled error: {$throwable->getMessage()}\n");
            return ExitCode::ERROR;
        }
    }

    private function printHelp(): void
    {
        $script = basename($_SERVER['argv'][0] ?? 'tool.php');

        echo "Usage:\n";
        echo "  php {$script} <command> [args...] [--options]\n\n";
        echo "Commands:\n";

        $max = 0;
        foreach ($this->commandsByName as $cmd) {
            $max = max($max, strlen($cmd->getName()));
        }

        foreach ($this->commandsByName as $cmd) {
            $name = str_pad($cmd->getName(), $max);
            echo "  {$name}  {$cmd->getDescription()}\n";
        }

        echo "\nGlobal options:\n";
        echo "  --help, -h  Show this help\n";
    }
}
