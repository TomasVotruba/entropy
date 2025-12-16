<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class SomeCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'some';
    }

    public function getDescription(): string
    {
        return 'command';
    }

    public function run(string $path, bool $flag, int $count): void
    {
    }
}
