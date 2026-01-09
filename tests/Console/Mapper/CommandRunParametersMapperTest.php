<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper;

use Entropy\Console\Mapper\CommandRunParametersMapper;
use Entropy\Tests\Console\Mapper\Fixture\SkipFilesCommand;
use PHPUnit\Framework\TestCase;

final class CommandRunParametersMapperTest extends TestCase
{
    private CommandRunParametersMapper $commandRunParametersMapper;

    protected function setUp(): void
    {
        $this->commandRunParametersMapper = new CommandRunParametersMapper();
    }

    public function test(): void
    {
        $argumentsAndOptions = $this->commandRunParametersMapper->map(new SkipFilesCommand());

        $this->assertCount(1, $argumentsAndOptions->getOptions());

        // test conversion of plural argument, to singular --option
        $skipFilesOption = $argumentsAndOptions->getOptions()[0];
        $this->assertSame('skip-file', $skipFilesOption->getName());
    }
}
