<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Output\CommandHelpPrinter;

use Entropy\Console\Output\CommandHelpPrinter;
use Entropy\Container\Container;
use Entropy\Tests\Console\Mapper\Fixture\SomeCommand;
use PHPUnit\Framework\TestCase;

final class CommandHelpPrinterTest extends TestCase
{
    public function test(): void
    {
        $container = new Container();
        $commandHelpPrinter = $container->make(CommandHelpPrinter::class);

        $helpDescription = $commandHelpPrinter->print(new SomeCommand());


        // show description of options

        $this->assertSame(
<<<HELP
Description:
  Command description

HELP
        , $helpDescription);
    }

}
