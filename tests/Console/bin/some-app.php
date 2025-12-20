<?php

declare(strict_types=1);

require __DIR__ . '/../../../vendor/autoload.php';

use Entropy\Console\ConsoleApplication;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Container\Container;
use Entropy\Tests\Console\ConsoleApplication\Fixture\SimpleCommand;

$container = new Container();

$container->service(
    SimpleCommand::class,
    fn (Container $container): SimpleCommand => new SimpleCommand($container->make(OutputPrinter::class))
);

$consoleApplication = $container->make(ConsoleApplication::class);
$consoleApplication->run($argv);
