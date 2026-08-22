<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Container\Fixture;

final class CollectedAggregate
{
    /**
     * @param CollectedInterface[] $collected
     */
    public function __construct(
        private array $collected
    ) {
    }

    /**
     * @return CollectedInterface[]
     */
    public function getCollected(): array
    {
        return $this->collected;
    }
}
