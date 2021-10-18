<?php

use DI\ContainerBuilder;
use Donchev\Log\Loggers\FileLogger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;

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

            Environment::class => function (ContainerInterface $container) {
                $loader = new Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/templates');

                $options = $container->get('settings')['env'] === 'prod'
                    ? ['cache' => dirname(__DIR__) . '/cache/twig'] : [];

                return new Environment($loader, $options);
            },

            'settings' => $settings
        ]
    );

    return $builder->build();
};
