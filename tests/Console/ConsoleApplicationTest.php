<?php

declare(strict_types=1);

namespace Entropy\Tests\Console;

use Entropy\Console\ConsoleApplication;
use Entropy\Console\Input\InputParser;
use Entropy\Console\Output\HelpPrinter;
use PHPUnit\Framework\TestCase;

final class ConsoleApplicationTest extends TestCase
{
    public function test(): void
    {
        $inputParser = new InputParser();
        $helpPrinter = new HelpPrinter();

        $consoleApplication = new ConsoleApplication($helpPrinter, $inputParser, []);

        $consoleApplication->run([]);
    }
}
