<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Console\Contract\CommandInterface;

final class HelpPrinter
{
    /**
     * @param CommandInterface[] $commands
     */
    public function printHelp(array $commands): void
    {
        $script = basename($_SERVER['argv'][0] ?? 'tool.php');

        echo "Usage:\n";
        echo "  php {$script} <command> [args...] [--options]\n\n";
        echo "Commands:\n";

        $maxCommandNameLength = $this->resolveCommandNameMaxLenght($commands);

        foreach ($commands as $command) {
            $name = str_pad($command->getName(), $maxCommandNameLength + 5);
            echo "  {$name}  {$command->getDescription()}\n";
        }

        echo "\nGlobal options:\n";
        echo "  --help, -h  Show this help\n";
    }

    /**
     * @param CommandInterface[] $commands
     */
    private function resolveCommandNameMaxLenght(array $commands): int
    {
        $maxCommandNameLength = 0;
        foreach ($commands as $command) {
            $maxCommandNameLength = max($maxCommandNameLength, strlen($command->getName()));
        }

        return $maxCommandNameLength;
    }
}
