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
    function (Container $container): SimpleCommand {
        return new SimpleCommand($container->make(OutputPrinter::class));
    }
);

//// @todo this should not be need, as another useless manual labour
//$container->service(CommandRegistry::class, function (Container $container): CommandRegistry {
//    $someCommand = $container->make(SimpleCommand::class);
//
//    return new CommandRegistry([$someCommand]);
//});

$consoleApplication = $container->make(ConsoleApplication::class);
$consoleApplication->run($argv);
