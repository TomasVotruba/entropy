<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\ValueObject\Argument;
use Entropy\Console\ValueObject\Option;
use Entropy\Tests\Console\Output\CommandHelpPrinter\CommandHelpPrinterTest;
use ReflectionMethod;

#[RelatedTest(CommandHelpPrinterTest::class)]
final readonly class CommandHelpPrinter
{
    public function __construct(
        private OutputPrinter $outputPrinter
    ) {
    }

    public function print(CommandInterface $command): string
    {
        $command->getName();

        $help = [];

        if ($command->getDescription() !== '') {
            $help[] = '<fg=yellow>Description:</>';
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

        $runReflectionMethod = new ReflectionMethod($command, 'run');

        $arguments = [];
        $options = [];

        foreach ($runReflectionMethod->getParameters() as $key => $reflectionParameter) {
            // 1st param is argument by convention
            if ($key === 0) {
                $arguments[] = new Argument(
                    $reflectionParameter->getName(),
                    $reflectionParameter->getType()
                        ->getName()
                );
            } else {
                $options[] = new Option(
                    $reflectionParameter->getName(),
                    $reflectionParameter->getType()
                        ->getName()
                );
            }
        }

        dump($arguments);
        dump($options);

        die;
    }
}
