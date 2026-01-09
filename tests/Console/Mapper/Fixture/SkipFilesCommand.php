<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class SkipFilesCommand implements CommandInterface
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
     * @param string[] $skipFiles
     */
    public function run(string $source, array $skipFiles = []): void
    {
    }
}
