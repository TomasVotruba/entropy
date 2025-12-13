<?php

$containerFactory = new ContainerFactory();
$container = $containerFactory->create();

$application = $container->make(Application::class);
exit($application->run());
