<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$settings = require_once dirname(__DIR__) . '/bootstrap/settings.php';

$containerBuilder = require_once dirname(__DIR__) . '/bootstrap/container.php';
$container = $containerBuilder($settings);

$db = require_once dirname(__DIR__) . '/bootstrap/db.php';
$db($container);

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $routeCollector) {
    $routeImporter = require_once dirname(__DIR__) . '/bootstrap/router.php';
    $routeImporter(require_once dirname(__DIR__) . '/config/routes.php', $routeCollector);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        return http_response_code(404);

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        return http_response_code(405);

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $parameters = $routeInfo[2];

        $container->call($handler, $parameters);
        break;
}
