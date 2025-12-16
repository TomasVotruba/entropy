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

        $help = [];

        if ($command->getDescription() !== '') {
            $help[] = $this->formatSection('Description:');
            $help[] = '  ' . $command->getDescription();
            $help[] = '';
        }

        // has arguments?
        //        $help[] = $this->formatSection('Usage:');
        //        $help[] = sprintf('  %s [options]', $name);
        //        $help[] = '';

        // Optional: print args/options if your CommandInterface provides them.
        // Keep it safe: only call when the method exists.
        if (method_exists($command, 'getArguments')) {
            /** @var array<string, string> $arguments */
            $arguments = (array) $command->getArguments();
            if ($arguments !== []) {
                $help[] = $this->formatSection('Arguments:');
                foreach ($arguments as $argName => $argDesc) {
                    $help[] = sprintf('  %-18s %s', $argName, $argDesc);
                }
                $help[] = '';
            }
        }

        if (method_exists($command, 'getOptions')) {
            /** @var array<string, string> $options */
            $options = (array) $command->getOptions();
            if ($options !== []) {
                $help[] = $this->outputPrinter->writeln('<fg=yellow>Options:</>');
                foreach ($options as $optName => $optDesc) {
                    // allow either "dry-run" or "--dry-run"
                    $optLabel = str_starts_with($optName, '-') ? $optName : '--' . $optName;
                    $help[] = sprintf('  %-18s %s', $optLabel, $optDesc);
                }
                $help[] = '';
            }
        }

        $text = implode(PHP_EOL, $help);

        // Print it (and return for tests)
        $this->outputPrinter->writeln($text);

        return $text;
    }

    private function formatSection(string $text): string
    {
        return $this->color($text, "\033[33m");
    }

    private function color(string $text, string $open): string
    {
        if (! method_exists($this->outputPrinter, 'supportsColors') || ! $this->outputPrinter->supportsColors()) {
            return $text;
        }

        return $open . $text . "\033[0m";
    }
}
