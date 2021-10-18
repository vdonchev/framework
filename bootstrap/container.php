<?php

use DI\ContainerBuilder;
use Donchev\Log\Loggers\FileLogger;
use Psr\Log\LoggerInterface;

return function (array $settings) {
    $builder = new ContainerBuilder();

    $builder->useAnnotations(true);

    if ($settings['env'] === 'prod') {
        $builder->enableCompilation(__DIR__ . '/../cache/container');
    }


    $builder->addDefinitions(
        [
            LoggerInterface::class => DI\create(FileLogger::class)->constructor(
                dirname(__DIR__) . '/logs/application.log'
            ),

            'settings' => $settings
        ]
    );

    return $builder->build();
};
