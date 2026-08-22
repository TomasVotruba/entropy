<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Container;

use Entropy\Container\Container;
use Entropy\Tests\Container\Container\Fixture\CollectedInterface;
use Entropy\Tests\Container\Container\Fixture\FirstCollected;
use Entropy\Tests\Container\Container\Fixture\SecondCollected;
use PHPUnit\Framework\TestCase;

final class ContainerDiscoveryTest extends TestCase
{
    public function testRegisterMakesClassDiscoverableByContract(): void
    {
        $container = new Container();
        $container->register(FirstCollected::class);
        $container->register(SecondCollected::class);

        $collected = $container->findByContract(CollectedInterface::class);

        $this->assertCount(2, $collected);
        $this->assertContainsOnlyInstancesOf(CollectedInterface::class, $collected);
    }

    public function testRegisterIsIdempotent(): void
    {
        $container = new Container();
        $container->register(FirstCollected::class);
        $container->register(FirstCollected::class);

        $collected = $container->findByContract(CollectedInterface::class);

        $this->assertCount(1, $collected);
    }

    public function testFindByContractReturnsList(): void
    {
        $container = new Container();
        $container->register(FirstCollected::class);
        $container->register(SecondCollected::class);

        $collected = $container->findByContract(CollectedInterface::class);

        $this->assertSame([0, 1], array_keys($collected));
    }
}
