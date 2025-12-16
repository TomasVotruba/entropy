<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Fixture;

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

    public function run(bool $dryRun = false): int
    {
        dump($dryRun);
        die;

        return ExitCode::SUCCESS;
    }
}
