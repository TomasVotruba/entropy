<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class ShortAliasCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'short-alias';
    }

    public function getDescription(): string
    {
        return 'Command description';
    }

    /**
     * @param string $config Path to config
     *
     * @option $config -c
     */
    public function run(bool $dryRun = false, string $config = ''): void
    {
    }
}
