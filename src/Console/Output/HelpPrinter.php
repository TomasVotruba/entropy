<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Console\CommandRegistry;
use Entropy\Console\Contract\CommandInterface;

final readonly class HelpPrinter
{
    public function __construct(private CommandRegistry $commandRegistry)
    {

    }

    public function printHelp(): void
    {
        $script = basename($_SERVER['argv'][0] ?? 'tool.php');

        echo "Usage:\n";
        echo "  php {$script} <command> [args...] [--options]\n\n";
        echo "Commands:\n";

        $maxCommandNameLength = $this->commandRegistry->getCommandNameMaxLength();

        foreach ($this->commandRegistry->all() as $command) {
            $name = str_pad($command->getName(), $maxCommandNameLength + 5);
            echo "  {$name}  {$command->getDescription()}\n";
        }

        echo "\nGlobal options:\n";
        echo "  --help, -h  Show this help\n";
    }
}
