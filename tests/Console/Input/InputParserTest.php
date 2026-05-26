<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Input;

use Entropy\Console\Input\InputParser;
use PHPUnit\Framework\TestCase;

final class InputParserTest extends TestCase
{
    private InputParser $inputParser;

    protected function setUp(): void
    {
        $this->inputParser = new InputParser();
    }

    public function test(): void
    {
        $cliRequest = $this->inputParser->parse(['bin/rector', 'process', 'src']);

        $this->assertSame('process', $cliRequest->getCommandName());
        $this->assertSame(['src'], $cliRequest->getArguments());
        $this->assertSame([], $cliRequest->getOptions());
    }

    public function testArrayOptions(): void
    {
        $cliRequest = $this->inputParser->parse(['bin/rector', 'process', 'src', '--skip', 'this', '--skip', 'that']);

        $this->assertSame([
            'skip' => ['this', 'that'],
        ], $cliRequest->getOptions());
    }

    public function testNumericOptionIsScalar(): void
    {
        $cliRequest = $this->inputParser->parse(['bin/rector', 'process', 'src', '--limit', '5']);

        $this->assertSame([
            'limit' => '5',
        ], $cliRequest->getOptions());
    }
}
