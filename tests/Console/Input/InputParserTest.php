<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Input;

use Entropy\Console\Input\InputParser;
use PHPUnit\Framework\TestCase;

final class InputParserTest extends TestCase
{
    public function test(): void
    {
        $inputParser = new InputParser();

        $cliRequest = $inputParser->parse(['bin/rector', 'process', 'src']);

        $this->assertSame('process', $cliRequest->getCommandName());
        $this->assertSame(['src'], $cliRequest->getArguments());
        $this->assertSame([], $cliRequest->getOptions());
    }

    public function testMultipleValuesForSameOption(): void
    {
        $inputParser = new InputParser();

        $cliRequest = $inputParser->parse([
            'bin/jack',
            'breakpoint',
            '--ignore=symfony/',
            '--ignore=doctrine/',
            '--ignore=phpunit/',
        ]);

        $this->assertSame('breakpoint', $cliRequest->getCommandName());
        $this->assertSame([], $cliRequest->getArguments());
        $this->assertSame([
            'ignore' => ['symfony/', 'doctrine/', 'phpunit/'],
        ], $cliRequest->getOptions());
    }

    public function testSingleOptionValueRemainsAsScalar(): void
    {
        $inputParser = new InputParser();

        $cliRequest = $inputParser->parse([
            'bin/jack',
            'breakpoint',
            '--ignore=symfony/',
            '--dev',
        ]);

        $this->assertSame('breakpoint', $cliRequest->getCommandName());
        $this->assertSame([], $cliRequest->getArguments());
        $this->assertSame([
            'ignore' => 'symfony/',
            'dev' => true,
        ], $cliRequest->getOptions());
    }

    public function testMultipleOptionsWithMixedFormats(): void
    {
        $inputParser = new InputParser();

        $cliRequest = $inputParser->parse([
            'bin/jack',
            'process',
            '--path=src',
            '--path=tests',
            '--dev',
            'extra-arg',
        ]);

        $this->assertSame('process', $cliRequest->getCommandName());
        $this->assertSame(['extra-arg'], $cliRequest->getArguments());
        $this->assertSame([
            'path' => ['src', 'tests'],
            'dev' => true,
        ], $cliRequest->getOptions());
    }
}
