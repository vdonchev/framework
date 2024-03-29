<?php

use DI\Container;
use DI\ContainerBuilder;
use Donchev\Framework\Flash\FlashService;
use Donchev\Framework\Twig\TwigFunctions;
use Donchev\Log\Loggers\FileLogger;
use Nette\Mail\Mailer;
use Nette\Mail\SmtpMailer;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

return function (array $settings) {
    $builder = new ContainerBuilder();

    $builder->useAnnotations(true);

    if ($settings['app.env'] === 'prod') {
        $builder->enableCompilation(__DIR__ . '/../var/cache/container');
    }

    $builder->addDefinitions(
        [
            LoggerInterface::class => DI\create(FileLogger::class)->constructor(
                dirname(__DIR__) . '/var/log/application.log'
            ),

            Environment::class => DI\factory(function (ContainerInterface $container) {
                $loader = new Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/templates');

                $options = $container->get('app.settings')['app.env'] === 'prod'
                    ? ['cache' => dirname(__DIR__) . '/var/cache/twig'] : [];

                if ($container->get('app.settings')['app.env'] === 'dev') {
                    $options['debug'] = true;
                }

                $twig = new Environment($loader, $options);

                if ($container->get('app.settings')['app.env'] === 'dev') {
                    $twig->addExtension(new DebugExtension());
                }

                $twig->addGlobal('http_get', $_GET);
                $twig->addGlobal('http_post', $_POST);
                $twig->addGlobal('http_session', $_SESSION);
                $twig->addGlobal('http_cookie', $_COOKIE);

                $flash = new FlashService();
                $f = new TwigFunctions($flash);
                $twigFunction = new TwigFunction('get_flash', [$f, 'getFlashes']);

                $twig->addFunction($twigFunction);

                return $twig;
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
                    $container->get('app.settings')['db.port'],
                    $container->get('app.settings')['db.encoding']
                );
            }),

            TagAwareCacheInterface::class => DI\factory(function (Container $container) {
                if ($container->get('app.settings')['app.env'] === 'dev') {
                    return new TagAwareAdapter(new ArrayAdapter());
                }

                return new TagAwareAdapter(
                    new FilesystemAdapter('', 0, dirname(__DIR__) . '/var/cache/filesystem')
                );
            }),

            CacheInterface::class => DI\factory(function (Container $container) {
                if ($container->get('app.settings')['app.env'] === 'dev') {
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
