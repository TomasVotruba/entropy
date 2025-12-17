<?php

declare(strict_types=1);

require __DIR__ . '/../../../vendor/autoload.php';

use Entropy\Console\ConsoleApplication;
use Entropy\Container\Container;

$container = new Container();

$container->service(\Entropy\Tests\Console\ConsoleApplication\Fixture\SimpleCommand::class, fn ()
=> new \Entropy\Tests\Console\ConsoleApplication\Fixture\SimpleCommand());

//// @todo this should not be need, as another useless manual labour
//$container->service(CommandRegistry::class, function (Container $container): CommandRegistry {
//    $someCommand = $container->make(SimpleCommand::class);
//
//    return new CommandRegistry([$someCommand]);
//});

$consoleApplication = $container->make(ConsoleApplication::class);
$consoleApplication->run($argv);
