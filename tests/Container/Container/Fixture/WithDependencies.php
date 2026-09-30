<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Container\Fixture;

final class WithDependencies
{
    private SomeType $someType;

    public function __construct(SomeType $someType)
    {
        $this->someType = $someType;
    }

    public function getSomeType(): SomeType
    {
        return $this->someType;
    }
}
