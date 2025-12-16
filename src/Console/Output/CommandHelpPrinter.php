<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Console\Contract\CommandInterface;

final readonly class CommandHelpPrinter
{
    public function __construct(
        private OutputPrinter $outputPrinter
    ) {

    }

    public function print(CommandInterface $command): void
    {
        dump($command);
        die;
    }
}
