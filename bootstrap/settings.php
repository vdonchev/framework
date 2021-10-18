<?php

use Symfony\Component\Yaml\Yaml;

$localSettingsFile = __DIR__ . '/../config/settings.local.yaml';

$settings = Yaml::parseFile(__DIR__ . '/../config/settings.yaml');

if (file_exists($localSettingsFile)) {
    $settings = array_merge($settings, Yaml::parseFile($localSettingsFile));
}

return $settings;
