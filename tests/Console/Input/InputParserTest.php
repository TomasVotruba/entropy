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

    public function testSingleStringOption(): void
    {
        $cliRequest = $this->inputParser->parse(['bin/rector', 'process', 'src', '--directory', 'some-path']);

        $this->assertSame([
            'directory' => ['some-path'],
        ], $cliRequest->getOptions());
    }

    public function testNumericOptionIsScalar(): void
    {
        $cliRequest = $this->inputParser->parse(['bin/rector', 'process', 'src', '--limit', '5']);

        $this->assertSame([
            'limit' => '5',
        ], $cliRequest->getOptions());
    }

    public function testFirstTokenShortFlag(): void
    {
        $cliRequest = $this->inputParser->parse(['bin/rector', '-h']);

        $this->assertNull($cliRequest->getCommandName());
        $this->assertSame([
            'h' => true,
        ], $cliRequest->getOptions());
    }

    public function testFirstTokenLongFlag(): void
    {
        $cliRequest = $this->inputParser->parse(['bin/rector', '--help']);

        $this->assertNull($cliRequest->getCommandName());
        $this->assertSame([
            'help' => true,
        ], $cliRequest->getOptions());
    }

    public function testFirstTokenLongOptionWithInlineValue(): void
    {
        $cliRequest = $this->inputParser->parse(['bin/rector', '--limit=5']);

        $this->assertNull($cliRequest->getCommandName());
        $this->assertSame([
            'limit' => '5',
        ], $cliRequest->getOptions());
    }

    public function testFirstTokenLongOptionWithSeparatedValue(): void
    {
        $cliRequest = $this->inputParser->parse(['bin/rector', '--directory', 'some-path']);

        $this->assertNull($cliRequest->getCommandName());
        $this->assertSame([], $cliRequest->getArguments());
        $this->assertSame([
            'directory' => 'some-path',
        ], $cliRequest->getOptions());
    }

    public function testFirstTokenLongOptionFollowedByMore(): void
    {
        $cliRequest = $this->inputParser->parse(['bin/rector', '--limit=5', '--directory', 'some-path']);

        $this->assertNull($cliRequest->getCommandName());
        $this->assertSame([
            'limit' => '5',
            'directory' => ['some-path'],
        ], $cliRequest->getOptions());
    }
}
