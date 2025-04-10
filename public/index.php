<?php

declare(strict_types=1);

use DI\Container;
use Donchev\Framework\Http\MiddlewareDispatcher;
use Donchev\Framework\Http\MiddlewareLoader;
use Donchev\Framework\Http\Session;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Log\LoggerInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$settings = require_once dirname(__DIR__) . '/bootstrap/settings.php';

// Init DI container
$containerBuilder = require_once dirname(__DIR__) . '/bootstrap/container.php';
/** @var Container $container */
$container = $containerBuilder($settings);

// Ensure session is started (handled inside Session factory)
if ($settings['app']['use_sessions']) {
    $container->get(Session::class);
}

// Setup routing
$dispatcher = FastRoute\cachedDispatcher(
    function (FastRoute\RouteCollector $routeCollector) {
        $routeImporter = require_once dirname(__DIR__) . '/bootstrap/router.php';
        $routeImporter(require_once dirname(__DIR__) . '/config/routes.php', $routeCollector);
    },
    [
        'cacheFile' => dirname(__DIR__) . '/var/cache/route.cache',
        'cacheDisabled' => $container->get('app.settings')['app']['env'] !== 'prod',
    ]
);

// Create PSR-7 request from globals
$request = ServerRequestFactory::fromGlobals();

// Dispatch route
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        return;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 Method Not Allowed';
        return;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $parameters = $routeInfo[2];

        $controllerInvoker = function ($request) use ($container, $handler, $parameters): HtmlResponse {
            ob_start();

            try {
                $container->call($handler, $parameters);
                $content = ob_get_clean();

                return new HtmlResponse($content);
            } catch (Throwable $e) {
                ob_end_clean(); //
                throw $e;
            }
        };

        $loader = $container->get(MiddlewareLoader::class);
        $middlewareDispatcher = new MiddlewareDispatcher($loader->load());

        try {
            $response = $middlewareDispatcher->handle($request, $controllerInvoker);

            http_response_code($response->getStatusCode());
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header("$name: $value", false);
                }
            }

            echo $response->getBody();
        } catch (Throwable $exception) {
            $container->get(LoggerInterface::class)->error($exception->getMessage());

            if ($settings['app']['env'] === 'dev') {
                $whoops = new Run();
                $whoops->pushHandler(new PrettyPageHandler());
                $whoops->writeToOutput(true);
                $whoops->allowQuit(false);
                $whoops->handleException($exception);
            } else {
                http_response_code(500);
                echo '500 Internal Server Error';
            }

            exit;
        }

        break;
}
