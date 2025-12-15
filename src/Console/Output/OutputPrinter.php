<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

final readonly class OutputPrinter
{
    private bool $useColors;

    private bool $isSilent;

    public function __construct(?bool $useColors = null)
    {
        $this->useColors = $useColors ?? $this->isTty();

        // avoid printing to stdout during unit tests
        $this->isSilent = defined('PHPUNIT_COMPOSER_INSTALL');
    }

    public function writeln(string $text, int $newlineCount = 0): void
    {
        if ($this->isSilent) {
            return;
        }

        // decorate colors if needed, e.g. <fg=green>text</>
        if (preg_match_all('/<fg=(green|yellow|red|cyan)>(.*?)<\/>/', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $coloredText = $this->color($match[2], $match[1]);
                $text = str_replace($match[0], $coloredText, $text);
            }
        }

        fwrite(STDOUT, $text . PHP_EOL);

        if ($newlineCount) {
            $this->newline($newlineCount);
        }
    }

    public function cyan(string $text): void
    {
        $this->writeln($this->color($text, 'cyan'));
    }

    public function yellow(string $text): void
    {
        $this->writeln($this->color($text, 'yellow'));
    }

    public function success(string $text): void
    {
        $this->writeln($this->color('✔ ' . $text, 'green'));
    }

    public function error(string $text): void
    {
        fwrite(STDERR, $this->color('✘ ' . $text, 'red') . PHP_EOL);
    }

    public function newline(int $count = 1): void
    {
        if ($this->isSilent) {
            return;
        }

        fwrite(STDOUT, str_repeat(PHP_EOL, $count));
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

    private function isTty(): bool
    {
        if (function_exists('stream_isatty')) {
            return @stream_isatty(STDOUT);
        }

        // Fallback: respect NO_COLOR if present
        return getenv('NO_COLOR') === false;
    }
}
