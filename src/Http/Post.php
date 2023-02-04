<?php

namespace Donchev\Framework\Http;

class Post
{
    public static function get(string $key)
    {
        return $_POST[$key] ?? null;
    }

    public static function exist(string $key): bool
    {
        return isset($_POST[$key]);
    }

    public static function empty(string $key): bool
    {
        return isset($_POST[$key]) && empty($_POST[$key]);
    }

    public static function unset(string $key): bool
    {
        if (isset($_POST[$key])) {
            unset($_POST[$key]);
            return true;
        }

        return false;
    }
}
