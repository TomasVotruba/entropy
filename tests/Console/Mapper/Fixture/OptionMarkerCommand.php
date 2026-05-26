<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class OptionMarkerCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'option-marker';
    }

    public function getDescription(): string
    {
        return 'Command that promotes its first param to an option';
    }

    /**
     * @option $source
     * @param string $source The source path
     * @param bool $verbose Be loud
     */
    public function run(string $source, bool $verbose = false): void
    {
    }
}
