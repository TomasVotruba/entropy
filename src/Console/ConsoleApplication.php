<?php

declare(strict_types=1);

namespace Entropy\Console;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Input\InputParser;
use Entropy\Console\Mapper\CLIRequestMapper;
use Entropy\Console\Output\CommandHelpFactory;
use Entropy\Console\Output\HelpPrinter;
use Entropy\Tests\Console\ConsoleApplication\ConsoleApplicationTest;

#[RelatedTest(ConsoleApplicationTest::class)]
final readonly class ConsoleApplication
{
    public function __construct(
        private HelpPrinter $helpPrinter,
        private CommandHelpFactory $commandHelpPrinter,
        private InputParser $inputParser,
        private CommandRegistry $commandRegistry,
        private CLIRequestMapper $cliRequestMapper,
    ) {
    }

    /**
     * @param mixed[] $argv
     * @return ExitCode::*
     */
    public function run(array $argv): int
    {
        $cliRequest = $this->inputParser->parse($argv);

        // global help
        if ($cliRequest->isHelp()) {
            $this->helpPrinter->print();
            return ExitCode::SUCCESS;
        }

        /** @var string $commandName */
        $commandName = $cliRequest->getCommandName();

        if (! $this->commandRegistry->has($commandName)) {
            fwrite(STDERR, sprintf("Unknown command: %s\n\n", $commandName));

            $this->helpPrinter->print();

            return ExitCode::INVALID_COMMAND;
        }

        try {
            $command = $this->commandRegistry->get($commandName);

            if ($cliRequest->isCommandHelp()) {
                // build command help here :)
                $this->commandHelpPrinter->build($command);
                return ExitCode::SUCCESS;
            }

            $arguments = $this->cliRequestMapper->resolveArguments($command, $cliRequest);
            return $command->run(...$arguments);

        } catch (\Throwable $throwable) {
            fwrite(STDERR, "Unhandled error: {$throwable->getMessage()}\n");
            return ExitCode::ERROR;
        }
    }
}
