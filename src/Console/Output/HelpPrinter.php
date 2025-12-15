<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Console\CommandRegistry;

final readonly class HelpPrinter
{
    public function __construct(
        private CommandRegistry $commandRegistry,
        private OutputPrinter $outputPrinter
    ) {

    }

    public function printHelp(): void
    {
        $script = basename($_SERVER['argv'][0] ?? 'tool.php');

        $this->outputPrinter->warning('Usage:');
        $this->outputPrinter->writeln(sprintf('  %s <command> [args...] [--options]', $script), 2);

        $this->outputPrinter->warning('Commands:');

        $maxCommandNameLength = $this->commandRegistry->getCommandNameMaxLength();

        foreach ($this->commandRegistry->all() as $command) {
            $name = str_pad($command->getName(), $maxCommandNameLength + 5);

            $this->outputPrinter->writeln(sprintf('  <fg=green>%s</>  %s', $name, $command->getDescription()));
        }

        $this->outputPrinter->newline();

        $this->outputPrinter->warning('Global options:');
        $this->outputPrinter->writeln(sprintf('  <fg=green>%s</>  %s', '--help, -h', 'Show this help'));
    }
}
