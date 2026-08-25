<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Container\Fixture;

final class FirstCollected implements CollectedInterface
{
    private ?CollectedAggregate $collectedAggregate = null;

    public function setCollectedAggregate(CollectedAggregate $collectedAggregate): void
    {
        $this->collectedAggregate = $collectedAggregate;
    }

    public function getCollectedAggregate(): ?CollectedAggregate
    {
        return $this->collectedAggregate;
    }
}
