<?php

declare(strict_types=1);

namespace Entropy\Tests\Console;

use Entropy\Console\InputParser;
use PHPUnit\Framework\TestCase;

final class InputParserTest extends TestCase
{
    public function test(): void
    {
        $inputParser = new InputParser();

        $argumentsAndOptions = $inputParser->parse(['bin/rector', 'process', 'src']);

        $this->assertSame('process', $argumentsAndOptions->getCommandName());
        $this->assertSame(['src'], $argumentsAndOptions->getArguments());
        $this->assertSame([], $argumentsAndOptions->getOptions());
    }
}
