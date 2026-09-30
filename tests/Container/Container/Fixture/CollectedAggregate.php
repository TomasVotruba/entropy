<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Container\Fixture;

final class CollectedAggregate
{
    /**
     * @var CollectedInterface[]
     */
    private array $collected;

    /**
     * @param CollectedInterface[] $collected
     */
    public function __construct(array $collected)
    {
        $this->collected = $collected;
    }

    /**
     * @return CollectedInterface[]
     */
    public function getCollected(): array
    {
        return $this->collected;
    }
}
