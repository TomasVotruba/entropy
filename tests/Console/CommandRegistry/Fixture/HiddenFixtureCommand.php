<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\CommandRegistry\Fixture;

use Entropy\Console\Contract\HiddenCommandInterface;

final class HiddenFixtureCommand implements HiddenCommandInterface
{
    public function getName(): string
    {
        return 'worker';
    }

    public function getDescription(): string
    {
        return 'A hidden, internal command';
    }

    public function run(): int
    {
        return 0;
    }
}
