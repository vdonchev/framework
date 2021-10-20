<?php

use DI\Container;
use DI\ContainerBuilder;
use Donchev\Log\Loggers\FileLogger;
use Nette\Mail\Mailer;
use Nette\Mail\SmtpMailer;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

return function (array $settings) {
    $builder = new ContainerBuilder();

    $builder->useAnnotations(true);

    if ($settings['app.env'] === 'prod') {
        $builder->enableCompilation(__DIR__ . '/../cache/container');
    }

    $builder->addDefinitions(
        [
            LoggerInterface::class => DI\create(FileLogger::class)->constructor(
                dirname(__DIR__) . '/logs/application.log'
            ),

            Environment::class => DI\factory(function (ContainerInterface $container) {
                $loader = new Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/templates');

                $options = $container->get('app.settings')['app.env'] === 'prod'
                    ? ['cache' => dirname(__DIR__) . '/cache/twig'] : [];

                return new Environment($loader, $options);
            }),

            Mailer::class => DI\factory(function (Container $container) {
                return new SmtpMailer(
                    [
                        'host' => $container->get('app.settings')['mail.host'],
                        'username' => $container->get('app.settings')['mail.username'],
                        'password' => $container->get('app.settings')['mail.password'],
                        'secure' => $container->get('app.settings')['mail.secure'],
                        'port' => $container->get('app.settings')['mail.port']
                    ]
                );
            }),

            MeekroDB::class => DI\factory(function (Container $container) {
                return new MeekroDB(
                    $container->get('app.settings')['db.host'],
                    $container->get('app.settings')['db.username'],
                    $container->get('app.settings')['db.password'],
                    $container->get('app.settings')['db.name'],
                    $container->get('app.settings')['db.port']
                );
            }),

            'db.main' => DI\get(MeekroDB::class),

            CacheInterface::class => DI\create(FilesystemAdapter::class)
                ->constructor('', 0, dirname(__DIR__) . '/cache/filesystem'),

            'app.cache' => DI\get(CacheInterface::class),

            'app.settings' => $settings,
        ]
    );

    return $builder->build();
};
