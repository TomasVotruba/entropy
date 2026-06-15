<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Attributes\RelatedTest;
use Entropy\Tests\Console\Output\ProgressBarTest;

/**
 * Lightweight progress bar rendered on a single, re-written terminal line.
 *
 * The rendering itself is a pure function (@see render()), so it can be unit
 * tested without writing to the terminal.
 *
 * @api used by console applications to report progress
 */
#[RelatedTest(ProgressBarTest::class)]
final class ProgressBar
{
    private const int BAR_WIDTH = 28;

    private int $current = 0;

    private int $maxSteps = 0;

    private readonly bool $isSilent;

    public function __construct()
    {
        // avoid printing to stdout during unit tests
        $this->isSilent = defined('PHPUNIT_COMPOSER_INSTALL');
    }

    public function start(int $maxSteps): void
    {
        $this->maxSteps = max(0, $maxSteps);
        $this->current = 0;

        $this->display();
    }

    public function advance(int $step = 1): void
    {
        $this->setProgress($this->current + $step);
    }

    public function setProgress(int $current): void
    {
        $this->current = max(0, min($current, $this->maxSteps));

        $this->display();
    }

    public function finish(): void
    {
        $this->current = $this->maxSteps;
        $this->display();

        if (! $this->isSilent) {
            fwrite(STDOUT, PHP_EOL);
        }
    }

    /**
     * Pure rendering of the current state, e.g. " 50% [==========>        ] 5/10"
     */
    public function render(): string
    {
        $percent = $this->resolvePercent();

        $completeWidth = (int) floor($percent * self::BAR_WIDTH);
        $hasArrow = $completeWidth < self::BAR_WIDTH;

        $bar = str_repeat('=', $completeWidth)
            . ($hasArrow ? '>' : '')
            . str_repeat(' ', max(0, self::BAR_WIDTH - $completeWidth - ($hasArrow ? 1 : 0)));

        return sprintf('%3d%% [%s] %d/%d', (int) round($percent * 100), $bar, $this->current, $this->maxSteps);
    }

    private function resolvePercent(): float
    {
        if ($this->maxSteps === 0) {
            return 1.0;
        }

        return $this->current / $this->maxSteps;
    }

    private function display(): void
    {
        if ($this->isSilent) {
            return;
        }

        // \r returns the cursor to the line start, so the bar is re-written in place
        fwrite(STDOUT, "\r" . $this->render());
    }
}
