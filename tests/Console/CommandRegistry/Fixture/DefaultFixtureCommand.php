<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\CommandRegistry\Fixture;

use Entropy\Console\Contract\DefaultCommandInterface;

final class DefaultFixtureCommand implements DefaultCommandInterface
{
    public function getName(): string
    {
        return 'check';
    }

    public function getDescription(): string
    {
        return 'The default command';
    }

    public function run(): int
    {
        return 0;
    }
}
