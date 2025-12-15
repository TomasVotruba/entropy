<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

final class OutputPrinter
{
    private bool $useColors;

    private bool $isSilent;

    public function __construct(?bool $useColors = null)
    {
        $this->useColors = $useColors ?? $this->isTty();

        // avoid printing to stdout during unit tests
        $this->isSilent = defined('PHPUNIT_COMPOSER_INSTALL');
    }

    public function writeln(string $text = ''): void
    {
        if ($this->isSilent) {
            return;
        }

        fwrite(STDOUT, $text . PHP_EOL);
    }

    public function info(string $text): void
    {
        $this->writeln($this->color($text, 'cyan'));
    }

    public function success(string $text): void
    {
        $this->writeln($this->color('✔ ' . $text, 'green'));
    }

    public function warning(string $text): void
    {
        $this->writeln($this->color('! ' . $text, 'yellow'));
    }

    public function error(string $text): void
    {
        fwrite(STDERR, $this->color('✘ ' . $text, 'red') . PHP_EOL);
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
