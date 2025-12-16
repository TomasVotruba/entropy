<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper;

use Entropy\Console\Exception\ConsoleInputMappingException;
use Entropy\Console\Mapper\CommandOptionsMapper;
use Entropy\Console\ValueObject\ArgumentsAndOptions;
use Entropy\Tests\Console\Mapper\Fixture\SomeCommand;
use PHPUnit\Framework\TestCase;

final class CommandOptionsMapperTest extends TestCase
{
    private SomeCommand $someCommand;

    private CommandOptionsMapper $commandOptionsMapper;

    protected function setUp(): void
    {
        $this->commandOptionsMapper = new CommandOptionsMapper();
        $this->someCommand = new SomeCommand();
    }

    public function testMapping(): void
    {
        $argumentsAndOptions = new ArgumentsAndOptions(
            'some',
            arguments: ['/some/path'],
            options: [
                'flag' => true,
                'count' => '5',
            ]
        );

        $arguments = $this->commandOptionsMapper->resolveArguments($this->someCommand, $argumentsAndOptions);
        $this->assertSame(['/some/path', true, 5], $arguments);
    }

    public function testExtraOption(): void
    {
        $argumentsAndOptions = new ArgumentsAndOptions(
            'some',
            arguments: ['/some/path'],
            options: [
                'flag' => true,
                'count' => '5',
                'extra-option' => 1234,
            ]
        );

        $this->expectException(ConsoleInputMappingException::class);
        $this->expectExceptionMessage('Unknown option: "--extra-option"');

        $this->commandOptionsMapper->resolveArguments($this->someCommand, $argumentsAndOptions);
    }

    public function testMissingOption(): void
    {
        $argumentsAndOptions = new ArgumentsAndOptions(
            'some',
            arguments: ['/some/path'],
            options: [
                'flag' => true,
            ]
        );

        $this->expectException(ConsoleInputMappingException::class);
        $this->expectExceptionMessage('Missing option: "--count"');

        $this->commandOptionsMapper->resolveArguments($this->someCommand, $argumentsAndOptions);

    }
}
