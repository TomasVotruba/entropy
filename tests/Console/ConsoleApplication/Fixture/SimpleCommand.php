<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\ConsoleApplication\Fixture;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;

final readonly class SimpleCommand implements CommandInterface
{
    public function __construct(
        private OutputPrinter $outputPrinter
    ) {
    }

    public function getName(): string
    {
        return 'test-me';
    }

    public function getDescription(): string
    {
        return 'Testing command';
    }

    /**
     * @param string[] $paths Paths to analyse.
     * @param bool $dryRun Show changes, but do not apply them.
     * @param string[] $skip List paths to skip.
     *
     * @return ExitCode::*
     */
    public function run(array $paths, bool $dryRun = false, array $skip = []): int
    {
        dump($paths);
        dump($dryRun);

        $this->outputPrinter->yellow('Yellow');
        $this->outputPrinter->green('Green');

        $this->outputPrinter->greenBackground('Success');
        $this->outputPrinter->orangeBackground('Warning');
        $this->outputPrinter->redBackground('Failure');

        return ExitCode::SUCCESS;
    }
}
