<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Mapper;

use Entropy\Console\Mapper\CommandRunParametersMapper;
use Entropy\Tests\Console\Mapper\Fixture\OptionMarkerCommand;
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

    public function testOptionMarkerPromotesFirstParamToOption(): void
    {
        $argumentsAndOptions = $this->commandRunParametersMapper->map(new OptionMarkerCommand());

        $this->assertCount(0, $argumentsAndOptions->getArguments());
        $this->assertCount(2, $argumentsAndOptions->getOptions());

        $sourceOption = $argumentsAndOptions->getOptions()[0];
        $this->assertSame('source', $sourceOption->getName());
        $this->assertSame('string', $sourceOption->getType());
    }
}
