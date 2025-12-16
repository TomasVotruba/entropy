<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper;

use Entropy\Console\Mapper\CommandOptionsMapper;
use Entropy\Console\ValueObject\ArgumentsAndOptions;
use Entropy\Tests\Console\Mapper\Fixture\SomeCommand;
use PHPUnit\Framework\TestCase;

final class CommandOptionsMapperTest extends TestCase
{
    private CommandOptionsMapper $commandOptionsMapper;

    protected function setUp(): void
    {
        $this->commandOptionsMapper = new CommandOptionsMapper();
    }


    public function test(): void
    {
        $someCommand = new SomeCommand();

        $argumentsAndOptions = new ArgumentsAndOptions('some',
            arguments: [
                '/some/path',
            ],
            options: [
            'flag' => true,
            'count' => '5',
        ]);

        $arguments = $this->commandOptionsMapper->resolveArguments(
            $someCommand,
            $argumentsAndOptions
        );

        $this->assertSame([
            '/some/path',
            true,
            5,
        ], $arguments);
    }
}
