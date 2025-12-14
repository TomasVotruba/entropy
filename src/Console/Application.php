<?php

declare(strict_types=1);

namespace Entropy\Console;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;

final class Application
{
    /**
     * @param CommandInterface[] $commands
     */
    public function __construct(
        private InputParser $inputParser,
        private array $commands
    ) {
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
