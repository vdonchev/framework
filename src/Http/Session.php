<?php

namespace Donchev\Framework\Http;

class Session
{
    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function unset(string $key)
    {
        unset($_SESSION[$key]);
    }

    public static function clear()
    {
        session_unset();
        session_destroy();
        session_write_close();
        session_start();
        session_regenerate_id(true);
    }
}
