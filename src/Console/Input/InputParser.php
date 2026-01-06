<?php

declare(strict_types=1);

namespace Entropy\Console\Input;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\ValueObject\CLIRequest;
use Entropy\Tests\Console\Input\InputParserTest;

#[RelatedTest(InputParserTest::class)]
final class InputParser
{
    /**
     * @param mixed[] $argv
     */
    public function parse(array $argv): CLIRequest
    {
        // remove script name
        array_shift($argv);

        if ($argv === []) {
            // fallback to show all commands
            return new CLIRequest(null);
        }

        $command = array_shift($argv);
        if (str_starts_with((string) $command, '-')) {
            // most likely an option
            $command = null;
            $options[ltrim((string) $command, '-')] = true;
        }

        $args = [];
        $options = [];

        while ($argv !== []) {
            $item = array_shift($argv);

            // --option or --option=value
            if (str_starts_with((string) $item, '--')) {
                [$name, $value] = $this->parseLongOption($item, $argv);
                $options[$name] = $value;
                continue;
            }

            // -v
            if (str_starts_with((string) $item, '-')) {
                $options[ltrim((string) $item, '-')] = true;
                continue;
            }

            // positional argument
            $args[] = $item;
        }

        return new CLIRequest($command, $args, $options);
    }

    /**
     * @param array<int, mixed> $argv
     * @return array{mixed, mixed}
     */
    private function parseLongOption(string $item, array &$argv): array
    {
        $item = ltrim($item, '--');

        if (str_contains($item, '=')) {
            return explode('=', $item, 2);
        }

        // --option value
        if ($argv !== [] && ! str_starts_with((string) $argv[0], '-')) {
            return [$item, array_shift($argv)];
        }

        return [$item, true];
    }
}
