<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Container\Fixture;

final class WithDependencies
{
    public function __construct(
        private SomeType $someType
    ) {
    }

    public function getSomeType(): SomeType
    {
        return $this->someType;
    }
}
