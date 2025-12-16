<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Contract\CommandInterface;

#[RelatedTest(\Entropy\Tests\Console\Output\CommandHelpPrinter\CommandHelpPrinterTest::class)]
final readonly class CommandHelpPrinter
{
    public function __construct(
        private OutputPrinter $outputPrinter
    ) {
    }

    public function print(CommandInterface $command): string
    {
        $name = $command->getName();
        $description = $command->getDescription();

        $script = basename($_SERVER['argv'][0] ?? 'console');

        $out = [];
        $out[] = '';
        $out[] = $this->formatTitle(sprintf('Command "%s"', $name));
        $out[] = '';

        if ($description !== '') {
            $out[] = $this->formatSection('Description:');
            $out[] = '  ' . $description;
            $out[] = '';
        }

        $out[] = $this->formatSection('Usage:');
        $out[] = sprintf('  %s %s [options] [--] [arguments]', $script, $name);
        $out[] = '';

        // Optional: print args/options if your CommandInterface provides them.
        // Keep it safe: only call when the method exists.
        if (method_exists($command, 'getArguments')) {
            /** @var array<string, string> $arguments */
            $arguments = (array) $command->getArguments();
            if ($arguments !== []) {
                $out[] = $this->formatSection('Arguments:');
                foreach ($arguments as $argName => $argDesc) {
                    $out[] = sprintf('  %-18s %s', $argName, $argDesc);
                }
                $out[] = '';
            }
        }

        if (method_exists($command, 'getOptions')) {
            /** @var array<string, string> $options */
            $options = (array) $command->getOptions();
            if ($options !== []) {
                $out[] = $this->formatSection('Options:');
                foreach ($options as $optName => $optDesc) {
                    // allow either "dry-run" or "--dry-run"
                    $optLabel = str_starts_with($optName, '-') ? $optName : '--' . $optName;
                    $out[] = sprintf('  %-18s %s', $optLabel, $optDesc);
                }
                $out[] = '';
            }
        }

        $out[] = $this->formatDim('Tip:') . ' ' . $this->formatDim(sprintf('Run "%s %s --help" to see this screen.', $script, $name));
        $out[] = '';

        $text = implode(PHP_EOL, $out);

        // Print it (and return for tests)
        $this->outputPrinter->writeln($text);

        return $text;
    }

    private function formatTitle(string $text): string
    {
        // Symfony-ish: bold headline
        return $this->color($text, "\033[1m");
    }

    private function formatSection(string $text): string
    {
        // Symfony-ish: yellow-ish section headers
        return $this->color($text, "\033[33m");
    }

    private function formatDim(string $text): string
    {
        // dim / gray
        return $this->color($text, "\033[2m");
    }

    private function color(string $text, string $open): string
    {
        if (!method_exists($this->outputPrinter, 'supportsColors') || !$this->outputPrinter->supportsColors()) {
            return $text;
        }

        return $open . $text . "\033[0m";
    }
}
