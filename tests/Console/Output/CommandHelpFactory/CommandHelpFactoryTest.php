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
    public function testBasic(): void
    {
        $container = new Container();
        $commandHelpPrinter = $container->make(CommandHelpFactory::class);

        $helpDescription = $commandHelpPrinter->build(new SomeCommand());

        // show description of options

        $this->assertSame(<<<HELP
  Command description

<fg=yellow>Arguments:</>
  <fg=green>path                  </>

<fg=yellow>Options:</>
  <fg=green>--flag                  </>  Enable extra features
  <fg=green>--count                 </>

HELP
            , $helpDescription);
    }

    public function testOptions(): void
    {
        $container = new Container();
        $commandHelpPrinter = $container->make(CommandHelpFactory::class);

        $helpDescription = $commandHelpPrinter->build(new AnotherCommand());

        $this->assertSame(<<<HELP
  Command description

<fg=yellow>Options:</>
  <fg=green>--has-flag              </>  Enable extra features, this is a required option
  <fg=green>--skip (many)           </>
  <fg=green>--limit </><fg=yellow>[10]</>

HELP
            , $helpDescription);

    }
}
