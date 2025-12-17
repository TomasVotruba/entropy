<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Tests\Console\Output\CommandHelpPrinter\CommandHelpPrinterTest;

#[RelatedTest(CommandHelpPrinterTest::class)]
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
            $help[] = '<fg=yellow>Description:</>'  ;
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
                $help[] = '<fg=yellow>Arguments:</>' . PHP_EOL;
                foreach ($arguments as $argName => $argDesc) {
                    $help[] = sprintf('  %-18s %s', $argName, $argDesc);
                }
                $help[] = '';
            }
        }

        // get options from the refletion!
        // @todo

        if (method_exists($command, 'getOptions')) {
            /** @var array<string, string> $options */
            $options = (array) $command->getOptions();
            if ($options !== []) {
                $help[] = '<fg=yellow>Options:</>' . PHP_EOL;
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
}
