<?php

use DI\ContainerBuilder;

return function (array $settings) {
    $builder = new ContainerBuilder();

    if ($settings['env'] === 'prod') {
        $builder->enableCompilation(__DIR__ . '/../cache/container.php');
    }

    return $builder->build();
};
