<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Output;

use Entropy\Console\Output\OutputColorizer;
use Entropy\Console\Output\ProgressBar;
use PHPUnit\Framework\TestCase;

final class ProgressBarTest extends TestCase
{
    private ProgressBar $progressBar;

    protected function setUp(): void
    {
        $this->progressBar = new ProgressBar(new OutputColorizer());
    }

    public function testStartRendersZeroPercent(): void
    {
        $this->progressBar->start(10);

        $this->assertStringContainsString('0%', $this->progressBar->render());
        $this->assertStringContainsString('0/10', $this->progressBar->render());
    }

    public function testAdvanceMovesProgress(): void
    {
        $this->progressBar->start(10);
        $this->progressBar->advance(5);

        $this->assertStringContainsString('50%', $this->progressBar->render());
        $this->assertStringContainsString('5/10', $this->progressBar->render());
    }

    public function testAdvanceNeverExceedsMax(): void
    {
        $this->progressBar->start(4);
        $this->progressBar->advance(10);

        $this->assertStringContainsString('100%', $this->progressBar->render());
        $this->assertStringContainsString('4/4', $this->progressBar->render());
    }

    public function testFinishReachesFullProgress(): void
    {
        $this->progressBar->start(7);
        $this->progressBar->finish();

        $this->assertStringContainsString('100%', $this->progressBar->render());
        $this->assertStringContainsString('7/7', $this->progressBar->render());
    }

    public function testZeroMaxStepsRendersComplete(): void
    {
        $this->progressBar->start(0);

        $this->assertStringContainsString('100%', $this->progressBar->render());
    }
}
