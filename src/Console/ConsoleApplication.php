<?php

declare(strict_types=1);

namespace Entropy\Console;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Exception\InvalidCommandException;
use Entropy\Console\Input\InputParser;
use Entropy\Tests\Console\ApplicationTest;

#[RelatedTest(ApplicationTest::class)]
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
        private HelpPrinter $helpPrinter,
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
        if ($argumentsAndOptions->isHelp()) {
            $this->helpPrinter->printHelp($this->commandsByName);
            return 0;
        }

        $commandName = $argumentsAndOptions->getCommandName();
        if (! isset($this->commandsByName[$commandName])) {
            fwrite(STDERR, sprintf("Unknown command: %s\n\n", $commandName));

            $this->helpPrinter->printHelp($this->commandsByName);

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
}
