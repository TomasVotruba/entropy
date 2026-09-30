<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class ArrayDefaultCommand implements CommandInterface
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
     * @param string[] $paths
     * @param string[] $fileExtension
     */
    public function run(array $paths, array $fileExtension = ['php']): void
    {
    }
}
