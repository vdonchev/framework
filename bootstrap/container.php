<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use Donchev\Framework\Factory\SessionFactory;
use Donchev\Framework\Factory\TwigFactory;
use Donchev\Framework\Http\Session;
use Donchev\Log\Loggers\FileLogger;
use Nette\Mail\Mailer;
use Nette\Mail\SmtpMailer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Twig\Environment;

return function (array $settings) {
    $builder = new ContainerBuilder();
    $builder->useAttributes(true);

    if ($settings['app']['env'] === 'prod') {
        $builder->enableCompilation(__DIR__ . '/../var/cache/container');
    }

    $builder->addDefinitions(
        [
            LoggerInterface::class => DI\create(FileLogger::class)->constructor($settings['app']['log_file']),

            Session::class => DI\factory([SessionFactory::class, 'create'])
                ->parameter('settings', DI\get('app.settings')),

            Environment::class => DI\factory([TwigFactory::class, 'create']),

            Mailer::class => DI\factory(function (Container $container) {
                return new SmtpMailer(
                    host: $container->get('app.settings')['mail']['host'],
                    username: $container->get('app.settings')['mail']['username'],
                    password: $container->get('app.settings')['mail']['password'],
                    port: $container->get('app.settings')['mail']['port'],
                    encryption: $container->get('app.settings')['mail']['secure']
                );
            }),

            MeekroDB::class => DI\factory(function (Container $container) {
                return new MeekroDB(
                    "mysql:host={$container->get('app.settings')['db']['host']};port={$container->get('app.settings')['db']['port']};dbname={$container->get('app.settings')['db']['name']};charset={$container->get('app.settings')['db']['charset']}",
                    $container->get('app.settings')['db']['username'],
                    $container->get('app.settings')['db']['password']
                );
            }),

            TagAwareCacheInterface::class => DI\factory(function (Container $container) {
                if ($container->get('app.settings')['app']['env'] === 'dev') {
                    return new TagAwareAdapter(new ArrayAdapter());
                }

                return new TagAwareAdapter(
                    new FilesystemAdapter('', 0, dirname(__DIR__) . '/var/cache/filesystem')
                );
            }),

            CacheInterface::class => DI\factory(function (Container $container) {
                if ($container->get('app.settings')['app']['env'] === 'dev') {
                    return new ArrayAdapter();
                }

                return new FilesystemAdapter('', 0, dirname(__DIR__) . '/var/cache/filesystem');
            }),

            'app.cache' => DI\get(CacheInterface::class),

            'app.settings' => $settings,
        ]
    );

    return $builder->build();
};
