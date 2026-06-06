<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class NullableArrayCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'nullable-array';
    }

    public function getDescription(): string
    {
        return 'Command description';
    }

    /**
     * @param string[]|null $skipFiles
     */
    public function run(string $source, ?array $skipFiles = null): void
    {
    }
}
