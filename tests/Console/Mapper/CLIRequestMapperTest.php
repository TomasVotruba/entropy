<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper;

use Entropy\Console\Exception\ConsoleInputMappingException;
use Entropy\Console\Mapper\CLIRequestMapper;
use Entropy\Console\ValueObject\CLIRequest;
use Entropy\Tests\Console\Mapper\Fixture\BoolCommand;
use Entropy\Tests\Console\Mapper\Fixture\OptionMarkerCommand;
use Entropy\Tests\Console\Mapper\Fixture\SkipFilesCommand;
use Entropy\Tests\Console\Mapper\Fixture\SomeCommand;
use PHPUnit\Framework\TestCase;

final class CLIRequestMapperTest extends TestCase
{
    private SomeCommand $someCommand;

    private BoolCommand $boolCommand;

    private CLIRequestMapper $cliRequestMapper;

    protected function setUp(): void
    {
        $this->cliRequestMapper = new CLIRequestMapper();
        $this->someCommand = new SomeCommand();
        $this->boolCommand = new BoolCommand();
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
        $this->assertSame([['/some/path', '/another-path'], true, 5, null], $arguments);
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

    public function testMissingArgument(): void
    {
        $cliRequest = new CLIRequest(
            'some',
            arguments: [],
            options: [
                'count' => '10',
                'flag' => true,
            ]
        );

        $this->expectException(ConsoleInputMappingException::class);
        $this->expectExceptionMessage('Missing required "path" argument');

        $this->cliRequestMapper->resolveArguments($this->someCommand, $cliRequest);
    }

    public function testOptionMarkerAcceptsLongOption(): void
    {
        $cliRequest = new CLIRequest(
            'option-marker',
            arguments: [],
            options: [
                'source' => '/some/path',
            ]
        );

        $arguments = $this->cliRequestMapper->resolveArguments(new OptionMarkerCommand(), $cliRequest);

        $this->assertSame(['/some/path', false], $arguments);
    }

    public function testOptionMarkerRejectsBarePositional(): void
    {
        $cliRequest = new CLIRequest('option-marker', arguments: ['/some/path']);

        $this->expectException(ConsoleInputMappingException::class);
        $this->expectExceptionMessage('Missing required value for "source" (use "--source" to provide it)');

        $this->cliRequestMapper->resolveArguments(new OptionMarkerCommand(), $cliRequest);
    }

    public function testBoolOptionsInArray(): void
    {
        $cliRequest = new CLIRequest(
            'bool',
            options: [
                'flag-string-true' => ['true'],
                'flag-string-false' => ['false'],
                'flag-bool-true' => [true],
                'flag-bool-false' => [false],
                'flag-int-true' => [1],
                'flag-int-false' => [0],
                'flag-null' => null,
                'flag-array-empty' => [],
                'flag-array-filled' => [
                    'a' => 1,
                ],
            ]
        );

        $arguments = $this->cliRequestMapper->resolveArguments($this->boolCommand, $cliRequest);
        $this->assertSame([true, false, true, false, true, false, false, false, true, true, false, null], $arguments);
    }

    public function testScalarOptionTakesLastValueFromArray(): void
    {
        $cliRequest = new CLIRequest(
            'option-marker',
            arguments: [],
            options: [
                'source' => ['/first/path', '/second/path'],
            ]
        );

        $arguments = $this->cliRequestMapper->resolveArguments(new OptionMarkerCommand(), $cliRequest);

        $this->assertSame(['/first/path', false], $arguments);
    }

    // @todo add --skip-file to $skipFiles mapping
    public function testSingularOptionToPluralParameter(): void
    {
        $cliRequest = new CLIRequest(
            'some',
            arguments: ['source'],
            options: [
                'skip-file' => ['first', 'second'],
            ]
        );

        $arguments = $this->cliRequestMapper->resolveArguments(new SkipFilesCommand(), $cliRequest);

        $this->assertSame(['source', ['first', 'second']], $arguments);
    }
}
