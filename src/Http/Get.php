<?php

namespace Donchev\Framework\Http;

class Get
{
    public static function getAll(bool $trim = true)
    {
        if ($trim === true) {
            return filter_var($_GET, FILTER_CALLBACK, ['options' => 'trim']);
        }

        return $_GET;
    }

    public static function get(string $key, bool $trim = true)
    {
        if (isset($_GET[$key])) {
            if ($trim === true) {
                return filter_var($_GET[$key], FILTER_CALLBACK, ['options' => 'trim']);
            }

            return $_GET[$key];
        }

        return null;
    }

    public static function exist(string $key): bool
    {
        return isset($_GET[$key]);
    }

    public static function exists(array $keys): bool
    {
        foreach ($keys as $key) {
            if (!isset($_GET[$key])) {
                return false;
            }
        }

        return true;
    }

    public static function empty(string $key): bool
    {
        return isset($_GET[$key]) && empty($_GET[$key]);
    }

    public static function unset(string $key): bool
    {
        if (isset($_GET[$key])) {
            unset($_GET[$key]);
            return true;
        }

        return false;
    }
}
