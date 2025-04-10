#!/usr/bin/env php
<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Donchev\Framework\Command\CacheClearCommand;
use Donchev\Framework\Command\LogClearCommand;
use Symfony\Component\Console\Application;

$settings = require_once dirname(__DIR__) . '/bootstrap/settings.php';

$containerBuilder = require_once dirname(__DIR__) . '/bootstrap/container.php';
$container = $containerBuilder($settings);

$application = new Application();

$application->add(new CacheClearCommand());
$application->add(new LogClearCommand($settings['app']['log_file']));

$application->run();
