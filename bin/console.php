#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Donchev\Framework\Command\CacheClearCommand;
use Symfony\Component\Console\Application;

$settings = require_once dirname(__DIR__) . '/bootstrap/settings.php';

$containerBuilder = require_once dirname(__DIR__) . '/bootstrap/container.php';
$container = $containerBuilder($settings);

$application = new Application();

$application->add(new CacheClearCommand($container));

$application->run();
