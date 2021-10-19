<?php

use DI\ContainerBuilder;
use Donchev\Log\Loggers\FileLogger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;
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

                $options = $container->get('app.settings')['env'] === 'prod'
                    ? ['cache' => dirname(__DIR__) . '/cache/twig'] : [];

                return new Environment($loader, $options);
            },

            CacheInterface::class => DI\create(FilesystemAdapter::class)
                ->constructor('', 0, dirname(__DIR__) . '/cache/filesystem'),

            'app.cache' => DI\get(CacheInterface::class),

            'app.settings' => $settings
        ]
    );

    return $builder->build();
};
