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
     * @param bool $flag Enable extra features, this is a required option
     */
    public function run(bool $flag): void
    {
    }
}
