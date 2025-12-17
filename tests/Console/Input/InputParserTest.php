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
}
