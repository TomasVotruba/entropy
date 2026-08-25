<?php

declare(strict_types=1);

namespace Entropy\Tests\Container\Container;

use Entropy\Container\Container;
use Entropy\Tests\Container\Container\Fixture\CollectedAggregate;
use Entropy\Tests\Container\Container\Fixture\CollectedInterface;
use Entropy\Tests\Container\Container\Fixture\FirstCollected;
use Entropy\Tests\Container\Container\Fixture\SecondCollected;
use Entropy\Tests\Container\Container\Fixture\SomeType;
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

    public function testForgetByContractRemovesRegisteredImplementations(): void
    {
        $container = new Container();
        $container->register(FirstCollected::class);
        $container->register(SecondCollected::class);

        $this->assertCount(2, $container->findByContract(CollectedInterface::class));

        $container->forgetByContract(CollectedInterface::class);

        $this->assertCount(0, $container->findByContract(CollectedInterface::class));
    }

    public function testForgetByContractRebuildsFreshInstance(): void
    {
        $container = new Container();
        $container->register(FirstCollected::class);

        $firstInstance = $container->make(FirstCollected::class);
        $container->forgetByContract(CollectedInterface::class);
        $secondInstance = $container->make(FirstCollected::class);

        $this->assertNotSame($firstInstance, $secondInstance);
    }

    public function testAfterResolvingRunsOncePerInstance(): void
    {
        $container = new Container();

        $callCount = 0;
        $container->afterResolving(SomeType::class, function (SomeType $someType, Container $container) use (
            &$callCount
        ): void {
            ++$callCount;
        });

        $container->make(SomeType::class);
        $container->make(SomeType::class);

        $this->assertSame(1, $callCount);
    }

    public function testAfterResolvingBreaksAggregateCycle(): void
    {
        $container = new Container();
        $container->register(FirstCollected::class);
        $container->register(SecondCollected::class);

        $container->afterResolving(CollectedInterface::class, function (
            CollectedInterface $collected,
            Container $container
        ): void {
            if ($collected instanceof FirstCollected) {
                $collected->setCollectedAggregate($container->make(CollectedAggregate::class));
            }
        });

        $collectedAggregate = $container->make(CollectedAggregate::class);
        $this->assertCount(2, $collectedAggregate->getCollected());

        $firstCollected = $container->make(FirstCollected::class);
        $this->assertSame($collectedAggregate, $firstCollected->getCollectedAggregate());
    }
}
