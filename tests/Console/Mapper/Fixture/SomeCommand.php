<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class SomeCommand implements CommandInterface
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
     * @param string[] $path Paths to analyse
     * @param bool $flag Enable extra features
     */
    public function run(array $path, bool $flag, int $count, ?string $version = null): void
    {
    }
}
