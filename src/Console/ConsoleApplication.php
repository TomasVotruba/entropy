<?php

declare(strict_types=1);

namespace Entropy\Console;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Input\InputParser;
use Entropy\Console\Mapper\CommandOptionsMapper;
use Entropy\Console\Output\HelpPrinter;
use Entropy\Tests\Console\ConsoleApplicationTest;

#[RelatedTest(ConsoleApplicationTest::class)]
final readonly class ConsoleApplication
{
    public function __construct(
        private HelpPrinter $helpPrinter,
        private InputParser $inputParser,
        private CommandRegistry $commandRegistry,
        private CommandOptionsMapper $commandOptionsMapper,
    ) {
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
            $this->helpPrinter->printHelp();
            return 0;
        }

        /** @var string $commandName */
        $commandName = $argumentsAndOptions->getCommandName();

        if (! $this->commandRegistry->has($commandName)) {
            fwrite(STDERR, sprintf("Unknown command: %s\n\n", $commandName));

            $this->helpPrinter->printHelp();

            return ExitCode::INVALID_COMMAND;
        }

        try {
            $command = $this->commandRegistry->get($commandName);

            $arguments = $this->commandOptionsMapper->resolveArguments($command, $argumentsAndOptions);
            return $command->run(...$arguments);

        } catch (\Throwable $throwable) {
            fwrite(STDERR, "Unhandled error: {$throwable->getMessage()}\n");
            return ExitCode::ERROR;
        }
    }
}
