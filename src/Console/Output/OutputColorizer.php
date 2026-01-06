<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Attributes\RelatedTest;
use Entropy\Tests\Console\Output\OutputColozierTest;

#[RelatedTest(OutputColozierTest::class)]
final readonly class OutputColorizer
{
    private bool $useColors;

    public function __construct()
    {
        $this->useColors = $useColors ?? $this->isTty();
    }

    /**
     * @api used in tests
     */
    public function colorize(string $text): string
    {
        $matches = [];

        // foreground colors: <fg=green>text</>
        if (preg_match_all('~<fg=(green|yellow|red|cyan)>(.*?)</>~su', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $text = str_replace($match[0], $this->color($match[2], $match[1]), $text);
            }
        }

        // background colors: <bg=green>text</>
        if (preg_match_all('/<bg=(green|yellow|red|cyan|orange)>(.*?)<\/>/', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $text = str_replace($match[0], $this->background($match[2], $match[1]), $text);
            }
        }

        return $text;
    }

    public function color(string $text, string $color): string
    {
        if (! $this->useColors) {
            return $text;
        }

        return match ($color) {
            'green' => "\033[32m{$text}\033[0m",
            'yellow' => "\033[33m{$text}\033[0m",
            'red' => "\033[31m{$text}\033[0m",
            'cyan' => "\033[36m{$text}\033[0m",
            default => $text,
        };
    }

    public function background(string $text, string $color): string
    {
        $text = $this->padding($text);

        if (! $this->useColors) {
            return $text;
        }

        return match ($color) {
            // background ; foreground
            'green' => "\033[42;30m{$text}\033[0m",             // black on green
            'yellow', 'orange' => "\033[43;30m{$text}\033[0m",  // black on yellow
            'red' => "\033[41;30m{$text}\033[0m",               // WHITE on red (important)
            default => $text,
        };
    }

    private function padding(string $text): string
    {
        return ' ' . $text . ' ';
    }

    private function isTty(): bool
    {
        if (function_exists('stream_isatty')) {
            return @stream_isatty(STDOUT);
        }

        // Fallback: respect NO_COLOR if present
        return getenv('NO_COLOR') === false;
    }
}
