<?php

use DI\Container;

return function (Container $container) {
    DB::$dbName = $container->get('app.settings')['db.name'];
    DB::$user = $container->get('app.settings')['db.username'];
    DB::$password = $container->get('app.settings')['db.password'];
};
