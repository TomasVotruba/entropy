<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper\Fixture;

use Entropy\Console\Contract\CommandInterface;

final class BoolCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'bool';
    }

    public function getDescription(): string
    {
        return 'Command description';
    }

    public function run(
        bool $flagStringTrue,
        bool $flagStringFalse,
        bool $flagBoolTrue,
        bool $flagBoolFalse,
        bool $flagIntTrue,
        bool $flagIntFalse,
        bool $flagNull,
        bool $flagArrayEmpty,
        bool $flagArrayFilled,
        ?bool $flagNotGivenTrue = true,
        ?bool $flagNotGivenFalse = false,
        ?bool $flagNotGivenNull = null,
    ): void {
    }
}
