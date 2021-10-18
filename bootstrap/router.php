<?php

use FastRoute\RouteCollector;

return function (array $routes, RouteCollector $routeCollector) {
    foreach ($routes as $route) {
        $routeCollector->addRoute(...$route);
    }
};
