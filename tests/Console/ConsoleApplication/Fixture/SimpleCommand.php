<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\ConsoleApplication\Fixture;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use InvalidArgumentException;

final class SimpleCommand implements CommandInterface
{
    private OutputPrinter $outputPrinter;

    public function __construct(OutputPrinter $outputPrinter)
    {
        $this->outputPrinter = $outputPrinter;
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
    public function run(array $paths, bool $dryRun = false, ?string $version = null, array $skip = []): int
    {
        dump($paths);
        dump($dryRun);
        dump($version);

        // default value should remain null
        if ($version !== null) {
            throw new InvalidArgumentException('Default value for "--version" should be null');
        }

        $this->outputPrinter->yellow('Yellow');
        $this->outputPrinter->green('Green');

        $this->outputPrinter->greenBackground('Success');
        $this->outputPrinter->orangeBackground('Warning');
        $this->outputPrinter->redBackground('Failure');

        return ExitCode::SUCCESS;
    }
}
