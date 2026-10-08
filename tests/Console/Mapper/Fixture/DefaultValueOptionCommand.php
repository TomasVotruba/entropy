<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class DefaultValueOptionCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'default-value-option';
    }

    public function getDescription(): string
    {
        return 'Command with a string option that has a default value';
    }

    /**
     * @param string[] $path Paths to analyse
     * @param string $outputFormat Select output format
     */
    public function run(array $path, string $outputFormat = 'console'): void
    {
    }
}
