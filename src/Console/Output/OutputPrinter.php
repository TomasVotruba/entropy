<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

final readonly class OutputPrinter
{
    private const LABEL_WIDTH = 7;

    private bool $useColors;

    private bool $isSilent;

    public function __construct(?bool $useColors = null)
    {
        $this->useColors = $useColors ?? $this->isTty();

        // avoid printing to stdout during unit tests
        $this->isSilent = defined('PHPUNIT_COMPOSER_INSTALL');
    }

    /**
     * Handle color background and foreground tags in the text
     * e.g. <fg=green>text</>, <bg=red>text</>
     */
    public function writeln(string $text, int $newlineCount = 0): void
    {
        if ($this->isSilent) {
            return;
        }

        // foreground colors: <fg=green>text</>
        if (preg_match_all('/<fg=(green|yellow|red|cyan)>(.*?)<\/>/', $text, $matches, PREG_SET_ORDER)) {
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

        fwrite(STDOUT, $text . PHP_EOL);

        if ($newlineCount !== 0) {
            $this->newline($newlineCount);
        }
    }

    /* ---------- Semantic helpers (SymfonyStyle-like) ---------- */

    public function success(string $text): void
    {
        $this->writeln($this->labeled('SUCCESS', $text, 'green'));
    }

    public function error(string $text): void
    {
        $this->writeln($this->labeled('ERROR', $text, 'red'));
    }

    public function warning(string $text): void
    {
        $this->writeln($this->labeled('WARNING', $text, 'yellow'));
    }

    public function info(string $text): void
    {
        $this->writeln($this->labeled('INFO', $text, 'cyan'));
    }

    /* ---------- Existing shortcuts ---------- */

    public function cyan(string $text): void
    {
        $this->writeln($this->color($text, 'cyan'));
    }

    public function yellow(string $text): void
    {
        $this->writeln($this->color($text, 'yellow'));
    }

    public function orangeBackground(string $text): void
    {
        $this->writeln($this->background($text, 'orange'));
    }

    public function greenBackground(string $text): void
    {
        $this->writeln($this->background($text, 'green'));
    }

    public function redBackground(string $text): void
    {
        $this->writeln($this->background($text, 'red'));
    }

    public function newline(int $count = 1): void
    {
        if ($this->isSilent) {
            return;
        }

        fwrite(STDOUT, str_repeat(PHP_EOL, $count));
    }

    /* ---------- Internals ---------- */

    private function labeled(string $label, string $message, string $color): string
    {
        $label = str_pad($label, self::LABEL_WIDTH, ' ', STR_PAD_RIGHT);

        return sprintf('%s %s', $this->background($label, $color), $message);
    }

    private function color(string $text, string $type): string
    {
        if (! $this->useColors) {
            return $text;
        }

        return match ($type) {
            'green' => "\033[32m{$text}\033[0m",
            'yellow' => "\033[33m{$text}\033[0m",
            'red' => "\033[31m{$text}\033[0m",
            'cyan' => "\033[36m{$text}\033[0m",
            default => $text,
        };
    }

    private function background(string $text, string $type): string
    {
        $text = $this->pad($text);

        if (! $this->useColors) {
            return $text;
        }

        return match ($type) {
            // background ; foreground
            'green' => "\033[42;30m{$text}\033[0m",             // black on green
            'yellow', 'orange' => "\033[43;30m{$text}\033[0m",  // black on yellow
            'cyan' => "\033[46;30m{$text}\033[0m",              // black on cyan
            'red' => "\033[41;30m{$text}\033[0m",               // WHITE on red (important)
            default => $text,
        };
    }

    private function pad(string $text): string
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
