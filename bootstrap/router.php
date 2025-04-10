<?php

declare(strict_types=1);

use FastRoute\RouteCollector;

return function (array $routes, RouteCollector $routeCollector) {
    foreach ($routes as $route) {
        $routeCollector->addRoute(...$route);
    }
};
