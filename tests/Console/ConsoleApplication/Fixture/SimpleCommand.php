<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\ConsoleApplication\Fixture;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;

final class SimpleCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'test-me';
    }

    public function getDescription(): string
    {
        return 'Testing command';
    }

    /**
     * @param string[] $paths
     * @param bool $dryRun Show changes, but do not apply them.
     *
     * @return ExitCode::*
     */
    public function run(array $paths, bool $dryRun = false): int
    {
        dump($paths);
        dump($dryRun);
        die;

        return ExitCode::SUCCESS;
    }
}
