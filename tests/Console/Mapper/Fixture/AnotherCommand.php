<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class AnotherCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'some-name';
    }

    public function getDescription(): string
    {
        return 'Command description';
    }

    /**
     * @param bool $hasFlag Enable extra features, this is a required option
     * @param string[] $skip
     */
    public function run(bool $hasFlag, array $skip = [], int $limit = 10): void
    {
    }
}
