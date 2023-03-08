<?php

namespace Donchev\Framework\Http;

use const FILTER_CALLBACK;

class Post
{
    public static function getAll(bool $trim = true)
    {
        if ($trim === true) {
            return filter_var($_POST, FILTER_CALLBACK, ['options' => 'trim']);
        }

        return $_POST;
    }

    public static function get(string $key, bool $trim = true)
    {
        if (isset($_POST[$key])) {
            if ($trim === true) {
                return filter_var($_POST[$key], FILTER_CALLBACK, ['options' => 'trim']);
            }

            return $_POST[$key];
        }

        return null;
    }

    public static function exist(string $key): bool
    {
        return isset($_POST[$key]);
    }

    public static function exists(array $keys): bool
    {
        foreach ($keys as $key) {
            if (!isset($_POST[$key])) {
                return false;
            }
        }

        return true;
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
