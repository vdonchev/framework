<?php

declare(strict_types=1);

namespace Donchev\Framework\Factory;

use Donchev\Framework\Flash\FlashService;
use Donchev\Framework\Http\Get;
use Donchev\Framework\Http\Post;
use Donchev\Framework\Http\Session;
use Donchev\Framework\Twig\TwigFunctions;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TwigFactory
{
    public static function create(ContainerInterface $container): Environment
    {
        $settings = $container->get('app.settings');

        $loader = new FilesystemLoader(dirname(__DIR__, 2) . '/templates');

        $options = [];
        if ($settings['app']['env'] === 'prod') {
            $options['cache'] = dirname(__DIR__, 2) . '/var/cache/twig';
        }

        if ($settings['app']['env'] === 'dev') {
            $options['debug'] = true;
        }

        $twig = new Environment($loader, $options);

        if ($settings['app']['env'] === 'dev') {
            $twig->addExtension(new DebugExtension());
        }

        $twig->addGlobal('settings', $settings);
        $twig->addGlobal('http_get', new Get());
        $twig->addGlobal('http_post', new Post());
        $twig->addGlobal('http_cookie', $_COOKIE);

        if ($settings['app']['use_sessions'] === true) {
            $twig->addGlobal('http_session', $container->get(Session::class));

            $flash = new FlashService($container->get(Session::class));
            $f = new TwigFunctions($flash);
            $twigFunction = new TwigFunction('get_flash', [$f, 'getFlashes']);
            $twig->addFunction($twigFunction);
        }

        return $twig;
    }
}
