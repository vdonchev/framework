<?php

namespace Donchev\Framework\Http;

class Get
{
    public static function get(string $key)
    {
        return $_GET[$key] ?? null;
    }

    public static function exist(string $key): bool
    {
        return isset($_GET[$key]);
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
