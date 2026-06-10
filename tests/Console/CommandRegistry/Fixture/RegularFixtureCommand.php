<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\CommandRegistry\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class RegularFixtureCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'list-checkers';
    }

    public function getDescription(): string
    {
        return 'A regular, visible command';
    }

    public function run(): int
    {
        return 0;
    }
}
