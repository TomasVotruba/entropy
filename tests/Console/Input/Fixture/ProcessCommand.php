<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Input\Fixture;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Contract\DefaultCommandInterface;

final class ProcessCommand implements CommandInterface, DefaultCommandInterface
{
    public function getName(): string
    {
        return 'process';
    }

    public function getDescription(): string
    {
        return 'Command description';
    }

    /**
     * @param string[] $paths
     * @param string[] $skip
     */
    public function run(
        array $paths = [],
        array $skip = [],
        ?string $directory = null,
        ?string $config = null,
        int $limit = 10,
        bool $clearCache = false,
        bool $fix = false
    ): void {
    }
}
