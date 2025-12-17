<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Output\CommandHelpFactory;

use Entropy\Console\Output\CommandHelpFactory;
use Entropy\Container\Container;
use Entropy\Tests\Console\Mapper\Fixture\SomeCommand;
use PHPUnit\Framework\TestCase;

final class CommandHelpFactoryTest extends TestCase
{
    public function test(): void
    {
        $container = new Container();
        $commandHelpPrinter = $container->make(CommandHelpFactory::class);

        $helpDescription = $commandHelpPrinter->build(new SomeCommand());

        // show description of options

        $this->assertSame(<<<HELP
<fg=yellow>Description:</>
  Command description

<fg=yellow>Usage:</>
  some-name [arguments] [options]

HELP
            , $helpDescription);
    }
}
