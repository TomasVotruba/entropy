<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Output\CommandHelpFactory;

use Entropy\Console\Output\CommandHelpFactory;
use Entropy\Container\Container;
use Entropy\Tests\Console\Mapper\Fixture\AnotherCommand;
use Entropy\Tests\Console\Mapper\Fixture\SomeCommand;
use PHPUnit\Framework\TestCase;

final class CommandHelpFactoryTest extends TestCase
{
    private CommandHelpFactory $commandHelpFactory;

    protected function setUp(): void
    {
        $container = new Container();

        $this->commandHelpFactory = $container->make(CommandHelpFactory::class);
    }

    public function testBasic(): void
    {
        $helpDescription = $this->commandHelpFactory->build(new SomeCommand());

        // show description of options

        $this->assertSame(<<<HELP
  Command description

<fg=yellow>Arguments:</>
  <fg=green>path</> <fg=yellow>(many)</>  Paths to analyse

<fg=yellow>Options:</>
  <fg=green>--flag            </>  Enable extra features
  <fg=green>--count           </>

HELP
            , $helpDescription);
    }

    public function testOptions(): void
    {
        $helpDescription = $this-> commandHelpFactory->build(new AnotherCommand());

        $this->assertSame(<<<HELP
  Command description

<fg=yellow>Options:</>
  <fg=green>--has-flag       </>  Enable extra features, this is a required option
  <fg=green>--skip</> <fg=yellow>(many)    </>
  <fg=green>--limit</><fg=yellow>=[10]     </>

HELP
            , $helpDescription);
    }
}
