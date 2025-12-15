<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Console\CommandRegistry;

final readonly class HelpPrinter
{
    private const int MIN_WIDTH = 10;

    public function __construct(
        private CommandRegistry $commandRegistry,
        private OutputPrinter $outputPrinter
    ) {

    }

    public function printHelp(): void
    {
        $this->outputPrinter->yellow('Commands:');

        $maxCommandNameLength = $this->commandRegistry->getCommandNameMaxLength();
        $firstColumnWith = max(self::MIN_WIDTH, $maxCommandNameLength) + 3;

        foreach ($this->commandRegistry->all() as $command) {
            $name = str_pad($command->getName(), $firstColumnWith);
            $this->outputPrinter->writeln(sprintf('  <fg=green>%s</>  %s', $name, $command->getDescription()));
        }

        $this->outputPrinter->newline();
        $this->outputPrinter->yellow('Options:');

        $optionName = str_pad('--help, -h', $firstColumnWith);
        $this->outputPrinter->writeln(sprintf('  <fg=green>%s</>  Show this help', $optionName));
    }
}
