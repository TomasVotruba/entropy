<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Fixture;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\ValueObject\ArgumentsAndOptions;

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

    public function run(ArgumentsAndOptions $argumentsAndOptions): int
    {
        return ExitCode::SUCCESS;
    }
}
