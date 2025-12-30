<?php

declare(strict_types=1);

require __DIR__ . '/../../../vendor/autoload.php';

use Entropy\Console\ConsoleApplication;
use Entropy\Container\Container;

$container = new Container();
$container->autodiscover(__DIR__ . '/../ConsoleApplication/Fixture');

$consoleApplication = $container->make(ConsoleApplication::class);
$consoleApplication->run($argv);
