<?php

namespace Entropy\Console;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\ValueObject\ArgumentsAndOptions;
use Entropy\Tests\Console\InputParserTest;

#[RelatedTest(InputParserTest::class)]
final class InputParser
{
    /**
     * @param mixed[] $argv
     */
    public function parse(array $argv): ArgumentsAndOptions
    {
        // remove script getName
        array_shift($argv);

        if ($argv === []) {
            // fallback to show all commands
            return new ArgumentsAndOptions(null);
        }

        $command = array_shift($argv);
        $args = [];
        $options = [];

        while ($argv !== []) {
            $item = array_shift($argv);

            // --option or --option=value
            if (str_starts_with($item, '--')) {
                [$name, $value] = self::parseLongOption($item, $argv);
                $options[$name] = $value;
                continue;
            }

            // -v
            if (str_starts_with($item, '-')) {
                $options[ltrim($item, '-')] = true;
                continue;
            }

            // positional argument
            $args[] = $item;
        }

        return new ArgumentsAndOptions($command, $args, $options);
    }

    /**
     * @param mixed[] $argv
     * @return array{mixed, mixed}
     */
    private static function parseLongOption(string $item, array &$argv): array
    {
        $item = ltrim($item, '--');

        if (str_contains($item, '=')) {
            return explode('=', $item, 2);
        }

        // --option value
        if ($argv !== [] && ! str_starts_with($argv[0], '-')) {
            return [$item, array_shift($argv)];
        }

        return [$item, true];
    }
}
