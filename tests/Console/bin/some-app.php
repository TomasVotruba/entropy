<?php

// simple manual test to run console app

require __DIR__ . '/../../../vendor/autoload.php';

use Entropy\Console\CommandRegistry;
use Entropy\Console\ConsoleApplication;
use Entropy\Container\Container;
use Entropy\Tests\Console\ConsoleApplication\Fixture\SimpleCommand;

$container = new Container(getcwd());

// @todo this should not be need, as another useless manual labour
$container->service(CommandRegistry::class, function (Container $container): CommandRegistry {
    $someCommand = $container->make(SimpleCommand::class);

    return new CommandRegistry([$someCommand]);
});

$consoleApplication = $container->make(ConsoleApplication::class);
$consoleApplication->run($argv);
