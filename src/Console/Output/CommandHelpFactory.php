<?php

declare(strict_types=1);

namespace Entropy\Console\Output;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Mapper\CommandRunParametersMapper;
use Entropy\Tests\Console\Output\CommandHelpFactory\CommandHelpFactoryTest;

#[RelatedTest(CommandHelpFactoryTest::class)]
final readonly class CommandHelpFactory
{
    public function __construct(
        private CommandRunParametersMapper $commandRunParametersMapper,
    ) {
    }

    public function build(CommandInterface $command): string
    {
        $command->getName();

        $help = [];

        if ($command->getDescription() !== '') {
            $help[] = '<fg=yellow>Description:</>';
            $help[] = '  ' . $command->getDescription();
            $help[] = '';
        }

        $argumentsAndOptions = $this->commandRunParametersMapper->map($command);

        // has arguments?
        $help[] = '<fg=yellow>Usage:</>';

        $help[] = sprintf(
            '  %s%s%s',
            $command->getName(),
            $argumentsAndOptions->getArguments() !== [] ? ' [arguments]' : '',
            $argumentsAndOptions->getOptions() !== [] ? ' [options]' : ''
        );
        $help[] = '';

        return implode(PHP_EOL, $help);
    }
}
