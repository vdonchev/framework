<?php

declare(strict_types=1);

namespace Donchev\Framework\Http;

class Cookie
{
    public const DAY = 86400;
    public const WEEK = 604800;
    public const MONTH = 2592000;
    public const YEAR = 31536000;

    public const DEFAULT_COOKIE_OPTIONS = [
        'expiry' => self::MONTH,
        'path' => '/',
        'domain' => true, // true => auto-detect from HTTP_HOST
        'secure' => false,
        'httponly' => false,
        'remove_global' => true
    ];

    public static function day(int $amount = 1): int
    {
        return self::calculate($amount, self::DAY);
    }

    public static function week(int $amount = 1): int
    {
        return self::calculate($amount, self::WEEK);
    }

    public static function month(int $amount = 1): int
    {
        return self::calculate($amount, self::MONTH);
    }

    public static function year(int $amount = 1): int
    {
        return self::calculate($amount, self::YEAR);
    }

    private static function calculate(int $amount, int $duration): int
    {
        return $duration * $amount;
    }

    public static function has(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }

    public static function isEmpty(string $key): bool
    {
        return empty($_COOKIE[$key]);
    }

    public static function get(string $key): ?string
    {
        return self::has($key) ? $_COOKIE[$key] : null;
    }

    public static function set(string $key, string $value, array $options = []): bool
    {
        if (headers_sent()) {
            return false;
        }

        $options = array_merge(self::DEFAULT_COOKIE_OPTIONS, $options);

        // Handle domain: true => auto, null => don't set, string => use it
        if ($options['domain'] === true) {
            $options['domain'] = '.' . ($_SERVER['HTTP_HOST'] ?? '');
        } elseif (empty($options['domain'])) {
            $options['domain'] = '';
        }

        $options['expiry'] = is_numeric($options['expiry'])
            ? time() + $options['expiry']
            : strtotime($options['expiry']);

        $success = setcookie(
            $key,
            $value,
            $options['expiry'],
            $options['path'],
            $options['domain'],
            $options['secure'],
            $options['httponly']
        );

        if ($success) {
            $_COOKIE[$key] = $value;
        }

        return $success;
    }

    public static function unset(string $key, array $options = []): bool
    {
        if (headers_sent()) {
            return false;
        }

        $options = array_merge(self::DEFAULT_COOKIE_OPTIONS, $options);

        if ($options['domain'] === true) {
            $options['domain'] = '.' . ($_SERVER['HTTP_HOST'] ?? '');
        } elseif (empty($options['domain'])) {
            $options['domain'] = '';
        }

        if (!empty($options['remove_global'])) {
            unset($_COOKIE[$key]);
        }

        return setcookie(
            $key,
            '',
            time() - 3600,
            $options['path'],
            $options['domain'],
            $options['secure'],
            $options['httponly']
        );
    }
}
