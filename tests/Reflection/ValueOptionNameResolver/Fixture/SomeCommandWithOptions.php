<?php

declare(strict_types=1);

namespace Entropy\Tests\Reflection\ValueOptionNameResolver\Fixture;

final class SomeCommandWithOptions
{
    /**
     * @param string[] $paths
     * @param string[] $skipFiles
     */
    public function run(
        array $paths,
        array $skipFiles = [],
        ?string $config = null,
        int $limit = 10,
        bool $clearCache = false,
        bool $fix = false
    ): void {
    }
}
