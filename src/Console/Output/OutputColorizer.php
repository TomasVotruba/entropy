<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Enum\Color;
use Entropy\Tests\Console\Output\OutputColozierTest;

#[RelatedTest(OutputColozierTest::class)]
final readonly class OutputColorizer
{
    private bool $useColors;

    public function __construct()
    {
        $this->useColors = $this->isTty();
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
                $content = $match[2];
                /** @var Color::* $color */
                $color = $match[1];
                $text = str_replace($match[0], $this->background($content, $color), $text);
            }
        }

        return $text;
    }

    /**
     * @param Color::* $color
     */
    public function color(string $text, string $color): string
    {
        if (! $this->useColors) {
            return $text;
        }

        return match ($color) {
            Color::GREEN => "\033[32m{$text}\033[0m",
            Color::YELLOW => "\033[33m{$text}\033[0m",
            Color::RED => "\033[31m{$text}\033[0m",
            Color::CYAN => "\033[36m{$text}\033[0m",
        };
    }

    /**
     * @param Color::* $color
     */
    public function background(string $text, string $color): string
    {
        $text = $this->padding($text);

        if (! $this->useColors) {
            return $text;
        }

        return match ($color) {
            // background ; foreground
            Color::GREEN => "\033[42;30m{$text}\033[0m",
            Color::YELLOW, 'orange' => "\033[43;30m{$text}\033[0m",
            // WHITE on red (important)
            Color::RED => "\033[41;30m{$text}\033[0m",
            Color::CYAN => "\033[46;30m{$text}\033[0m",
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
