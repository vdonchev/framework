<?php

use DI\ContainerBuilder;
use Donchev\Log\Loggers\FileLogger;
use Psr\Log\LoggerInterface;

return function (array $settings) {
    $builder = new ContainerBuilder();

    if ($settings['env'] === 'prod') {
        $builder->enableCompilation(__DIR__ . '/../cache/container.php');
    }

    $fileLogger = new FileLogger(dirname(__DIR__) . '/logs/application.log');

    $builder->addDefinitions(
        [
            LoggerInterface::class => $fileLogger
        ]
    );

    return $builder->build();
};
