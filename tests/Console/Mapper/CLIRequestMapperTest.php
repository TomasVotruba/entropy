<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper;

use Entropy\Console\Exception\ConsoleInputMappingException;
use Entropy\Console\Mapper\CLIRequestMapper;
use Entropy\Console\ValueObject\CLIRequest;
use Entropy\Tests\Console\Mapper\Fixture\SomeCommand;
use PHPUnit\Framework\TestCase;

final class CLIRequestMapperTest extends TestCase
{
    private SomeCommand $someCommand;

    private CLIRequestMapper $cliRequestMapper;

    protected function setUp(): void
    {
        $this->cliRequestMapper = new CLIRequestMapper();
        $this->someCommand = new SomeCommand();
    }

    public function testMultipleArgs(): void
    {
        $cliRequest = new CLIRequest(
            'some',
            arguments: ['/some/path', '/another-path'],
            options: [
                'flag' => true,
                'count' => '5',
            ]
        );

        $arguments = $this->cliRequestMapper->resolveArguments($this->someCommand, $cliRequest);
        $this->assertSame([['/some/path', '/another-path'], true, 5], $arguments);
    }

    public function testExtraOption(): void
    {
        $cliRequest = new CLIRequest(
            'some',
            arguments: [['/some/path']],
            options: [
                'flag' => true,
                'count' => '5',
                'extra-option' => 1234,
            ]
        );

        $this->expectException(ConsoleInputMappingException::class);
        $this->expectExceptionMessage('Unknown option: "--extra-option"');

        $this->cliRequestMapper->resolveArguments($this->someCommand, $cliRequest);
    }

    public function testMissingOption(): void
    {
        $cliRequest = new CLIRequest(
            'some',
            arguments: [['/some/path']],
            options: [
                'flag' => true,
            ]
        );

        $this->expectException(ConsoleInputMappingException::class);
        $this->expectExceptionMessage('Missing required value for "count" (use "--count" to provide it)');

        $this->cliRequestMapper->resolveArguments($this->someCommand, $cliRequest);

    }
}
