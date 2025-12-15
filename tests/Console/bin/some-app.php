<?php

// simple manual test to run console app

require __DIR__ . '/../../../vendor/autoload.php';

use Entropy\Console\CommandRegistry;
use Entropy\Container\Container;
use Entropy\Tests\Console\Fixture\SimpleCommand;

$container = new Container(getcwd());
//$container->service(CommandRegistry::class, function (Container $container): CommandRegistry {
//    $someCommand = $container->make(SimpleCommand::class);
//
//    return new CommandRegistry([$someCommand]);
//});

$consoleApplication = $container->make(\Entropy\Console\ConsoleApplication::class);
$consoleApplication->run($argv);
