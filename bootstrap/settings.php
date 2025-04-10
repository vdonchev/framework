<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

$baseDir = realpath(dirname(__DIR__));

$localSettingsFile = $baseDir . '/config/settings.local.yaml';
$settings = Yaml::parseFile($baseDir . '/config/settings.yaml');

if (file_exists($localSettingsFile)) {
    $settings = array_merge($settings, Yaml::parseFile($localSettingsFile));
}

$placeholders = [
    '%base_dir%' => $baseDir,
    '%src_dir%' => $baseDir . '/src',
    '%var_dir%' => $baseDir . '/var',
];

array_walk_recursive($settings, function (&$value) use ($placeholders) {
    if (!is_string($value)) {
        return;
    }

    foreach ($placeholders as $key => $realPath) {
        $value = str_replace($key, $realPath, $value);
    }
});

return $settings;
